<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * =========================================================================
 * GamificationEngine  —  Universal Gamification Library
 * =========================================================================
 *
 * A plug-and-play gamification engine for CodeIgniter 3.
 * Completely module-agnostic: points accumulate across ALL modules.
 *
 * ── HOW ANY MODULE USES THIS ─────────────────────────────────────────────
 *
 *   // 1. Load in any controller
 *   $this->load->library('GamificationEngine');
 *
 *   // 2. Award points with one call
 *   $this->gamificationengine->award(
 *       $user_id,            // INT   — users.id
 *       'faq.approved',      // STRING — gam_actions.code
 *       $faq_id,             // INT   — source record PK (optional)
 *       'FAQ disetujui: ...' // STRING — human note (optional)
 *   );
 *
 *   // 3. That's it. Points, levels, badges, period scores all update.
 *
 * ── ADDING A NEW MODULE ───────────────────────────────────────────────────
 *
 *   INSERT INTO gam_modules (code, name) VALUES ('mymodule', 'My Module');
 *   INSERT INTO gam_actions (module_id, code, label, point_value) VALUES
 *     (LAST_INSERT_ID(), 'mymodule.done', 'Task Completed', 25);
 *
 *   Then call: $this->gamificationengine->award($uid, 'mymodule.done', $task_id);
 *   Zero other changes needed anywhere.
 *
 * =========================================================================
 */
class GamificationEngine {

    /** @var CI_Controller */
    protected $CI;

    /** @var array  In-memory action cache [code => row] */
    private $_action_cache = [];

    /** @var array  In-memory active-periods cache keyed by date */
    private $_period_cache = [];

    // ── Level thresholds (configurable via gam_config.php) ───────────────────
    // Note: Using static properties instead of constants for PHP 5.6 compatibility

    private static $LEVELS = [
         1 =>    0,
         2 =>  100,
         3 =>  250,
         4 =>  500,
         5 =>  900,
         6 => 1400,
         7 => 2000,
         8 => 3000,
         9 => 4500,
        10 => 6500,
    ];

    private static $LEVEL_TITLES = [
         1 => 'Pendatang Baru',
         2 => 'Penjelajah',
         3 => 'Kontributor',
         4 => 'Penolong',
         5 => 'Ahli',
         6 => 'Mentor',
         7 => 'Otoritas',
         8 => 'Master',
         9 => 'Bijaksana',
        10 => 'Guru Pengetahuan',
    ];

    // =========================================================================
    // CONSTRUCTOR
    // =========================================================================

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    // =========================================================================
    // PRIMARY PUBLIC API
    // =========================================================================

    /**
     * Award points to a user for an action.
     *
     * @param  int         $user_id     users.id
     * @param  string      $action_code gam_actions.code  e.g. 'faq.approved'
     * @param  int|null    $source_id   PK of the triggering record (optional)
     * @param  string|null $note        Human-readable note for the log
     * @param  string|null $date        Override date (Y-m-d), defaults to today
     * @param  array       $stat_increments  Extra module stats to increment
     *                                  e.g. ['faq_approved' => 1]
     * @return array  ['points' => int, 'new_level' => bool, 'new_badges' => array]
     */
    public function award(
        $user_id,
        $action_code,
        $source_id       = null,
        $note            = null,
        $date            = null,
        $stat_increments = []
    ) {
        $date   = $date ?: date('Y-m-d');
        $action = $this->_get_action($action_code);

        if (!$action || !$action->is_active) {
            return ['points' => 0, 'new_level' => false, 'new_badges' => []];
        }

        // Resolve active periods
        $active_periods = $this->_get_active_periods($date);

        // Resolve actual point value (check period overrides)
        $pts = $this->_resolve_points($action, $active_periods);

        // 1. Write to audit log (single row — period breakdown via JOIN on query)
        $this->CI->db->insert('gam_point_log', [
            'user_id'     => $user_id,
            'action_id'   => $action->id,
            'module_code' => $action->module_code,
            'action_code' => $action_code,
            'source_id'   => $source_id,
            'points'      => $pts,
            'note'        => $note,
            'created_at'  => $date . ' ' . date('H:i:s'),
        ]);

        // 2. Update lifetime score
        $prev_level = $this->_upsert_score($user_id, null, $pts, $action->module_code);

        // 3. Update each active period score
        foreach ($active_periods as $period) {
            $this->_upsert_score($user_id, $period->id, $pts, $action->module_code);
        }

        // 4. Increment module stats (lifetime + per period)
        if (!empty($stat_increments)) {
            $all_period_ids = array_merge([null], array_column($active_periods, 'id'));
            foreach ($all_period_ids as $pid) {
                foreach ($stat_increments as $stat_key => $delta) {
                    $this->_increment_stat($user_id, $action->module_code, $pid, $stat_key, $delta);
                }
            }
        }

        // 5. Check new level
        $new_score   = $this->get_user_score($user_id);
        $new_level   = $new_score ? (int)$new_score->level : 1;
        $level_up    = ($new_level > $prev_level);

        // 6. Check badges
        $new_badges  = $this->check_badges($user_id, $active_periods, $source_id);

        return [
            'points'      => $pts,
            'new_level'   => $level_up ? $new_level : false,
            'new_badges'  => $new_badges,
        ];
    }

    /**
     * Manually deduct points (e.g. penalty for rejection).
     * Internally calls award() with a negative action code.
     */
    public function deduct($user_id, $action_code, $source_id = null, $note = null) {
        return $this->award($user_id, $action_code, $source_id, $note);
    }

    // =========================================================================
    // SCORE ACCESS
    // =========================================================================

    /**
     * Get a user's score row.
     * @param  int       $user_id
     * @param  int|null  $period_id  NULL = lifetime
     * @return object|null
     */
    public function get_user_score($user_id, $period_id = null) {
        $q = $this->CI->db->where('user_id', (int)$user_id);
        if ($period_id) {
            $q->where('period_id', (int)$period_id);
        } else {
            $q->where('period_id IS NULL', null, false);
        }
        return $q->get('gam_user_scores')->row();
    }

    /**
     * Get all period scores for a user (for profile page).
     */
    public function get_user_period_scores($user_id) {
        return $this->CI->db
            ->select('s.*, p.label, p.period_type, p.period_code, p.year, p.quarter, p.semester, p.start_date, p.end_date, p.is_active')
            ->from('gam_user_scores s')
            ->join('gam_periods p', 'p.id = s.period_id')
            ->where('s.user_id', (int)$user_id)
            ->where('s.total_points >', 0)
            ->order_by('p.start_date', 'DESC')
            ->get()->result();
    }

    /**
     * Get all period scores for a user across all years (for "All Periods" tab).
     * Returns scores for periods where user has any activity.
     */
    public function get_all_period_scores($user_id) {
        return $this->CI->db
            ->select('s.*, p.label, p.period_type, p.period_code, p.year, p.quarter, p.semester, p.start_date, p.end_date, p.is_active')
            ->from('gam_user_scores s')
            ->join('gam_periods p', 'p.id = s.period_id')
            ->where('s.user_id', (int)$user_id)
            ->order_by('p.year', 'DESC')
            ->order_by('p.start_date', 'DESC')
            ->get()->result();
    }

    /**
     * Get a module stat value for a user.
     * @param  int       $user_id
     * @param  string    $module_code
     * @param  string    $stat_key
     * @param  int|null  $period_id   NULL = lifetime
     * @return int
     */
    public function get_stat($user_id, $module_code, $stat_key, $period_id = null) {
        $q = $this->CI->db->where([
            'user_id'     => (int)$user_id,
            'module_code' => $module_code,
            'stat_key'    => $stat_key,
        ]);
        if ($period_id) {
            $q->where('period_id', (int)$period_id);
        } else {
            $q->where('period_id IS NULL', null, false);
        }
        $row = $q->get('gam_user_module_stats')->row();
        return $row ? (int)$row->stat_value : 0;
    }

    /**
     * Get all stats for a user, optionally filtered by module.
     * Returns ['faq' => ['faq_approved' => 5, ...], 'doc' => [...]]
     */
    public function get_all_stats($user_id, $period_id = null) {
        $q = $this->CI->db->where('user_id', (int)$user_id);
        if ($period_id) {
            $q->where('period_id', (int)$period_id);
        } else {
            $q->where('period_id IS NULL', null, false);
        }
        $rows   = $q->get('gam_user_module_stats')->result();
        $result = [];
        foreach ($rows as $r) {
            $result[$r->module_code][$r->stat_key] = (int)$r->stat_value;
        }
        return $result;
    }

    /**
     * Get session-ready gamification data for a user.
     */
    public function get_session_data($user_id) {
        $score = $this->get_user_score($user_id);
        return [
            'gam_points' => $score ? (int)$score->total_points : 0,
            'gam_level'  => $score ? (int)$score->level        : 1,
            'gam_badge'  => $score ? $score->badge             : self::$LEVEL_TITLES[1],
        ];
    }

    // =========================================================================
    // PROFILE
    // =========================================================================

    /**
     * Build a full gamification profile for a user.
     * Fetches from `users` table + all gam_ tables.
     */
    public function get_profile($user_id) {
        $user = $this->CI->db->select(
            'u.id, u.username, u.nama_lengkap, u.email, u.photo, u.nip,
             u.jabatan_id, u.struktur_organisasi_id'
        )->from('users u')->where('u.id', (int)$user_id)->get()->row();

        if (!$user) return null;

        $lifetime = $this->get_user_score($user_id);

        $user->lifetime_points  = $lifetime ? (int)$lifetime->total_points : 0;
        $user->lifetime_level   = $lifetime ? (int)$lifetime->level        : 1;
        $user->lifetime_badge   = $lifetime ? $lifetime->badge             : self::$LEVEL_TITLES[1];
        $user->level_progress   = $this->level_progress($user->lifetime_points);
        $next_lvl               = min($user->lifetime_level + 1, 10);
        $user->next_level_pts   = isset(self::$LEVELS[$next_lvl]) ? self::$LEVELS[$next_lvl] : null;
        $user->level_title      = isset(self::$LEVEL_TITLES[$user->lifetime_level]) ? self::$LEVEL_TITLES[$user->lifetime_level] : self::$LEVEL_TITLES[10];

        // Module breakdown from lifetime score JSON
        $user->module_breakdown = [];
        if ($lifetime && $lifetime->breakdown) {
            $user->module_breakdown = json_decode($lifetime->breakdown, true) ?: [];
        }

        // Period scores
        $user->period_scores = $this->get_user_period_scores($user_id);

        // Rank in each period
        $user->period_ranks = [];
        foreach ($user->period_scores as $ps) {
            $rank = $this->CI->db
                ->where('period_id', $ps->period_id)
                ->where('total_points >', $ps->total_points)
                ->count_all_results('gam_user_scores') + 1;
            $user->period_ranks[$ps->period_id] = $rank;
        }

        // All stats grouped by module
        $user->all_stats = $this->get_all_stats($user_id);

        // Badges
        $user->badges = $this->CI->db
            ->select('b.*, ub.earned_at, p.label as period_label, p.period_type')
            ->from('gam_user_badges ub')
            ->join('gam_badges b', 'b.id = ub.badge_id')
            ->join('gam_periods p', 'p.id = ub.period_id', 'left')
            ->where('ub.user_id', (int)$user_id)
            ->order_by('ub.earned_at', 'DESC')
            ->get()->result();

        // FAQ stats from faq_items table
        $user->lifetime_approved = $this->CI->db
            ->where('user_id', (int)$user_id)
            ->where('status', 'approved')
            ->count_all_results('faq_items');
        
        $user->pending_count = $this->CI->db
            ->where('user_id', (int)$user_id)
            ->where('status', 'pending')
            ->count_all_results('faq_items');

        // Recent activity (lifetime log only — no period dupes)
        $user->activity = $this->CI->db
            ->select('l.*, m.name as module_name')
            ->from('gam_point_log l')
            ->join('gam_actions a', 'a.id = l.action_id', 'left')
            ->join('gam_modules m', 'm.code = l.module_code', 'left')
            ->where('l.user_id', (int)$user_id)
            ->order_by('l.created_at', 'DESC')
            ->limit(20)
            ->get()->result();

        // All period scores for all years (for the "All Periods" tab)
        $user->all_period_scores = $this->get_all_period_scores($user_id);

        return $user;
    }

    // =========================================================================
    // LEADERBOARD
    // =========================================================================

    /**
     * Get leaderboard for a period (or lifetime).
     *
     * @param  int|null $period_id  NULL = lifetime
     * @param  int      $limit
     * @param  string|null $module_code  Filter by module contribution (optional)
     * @return array
     */
    public function get_leaderboard($period_id = null, $limit = 10, $module_code = null) {
        $this->CI->db->select(
            'u.id, u.username, u.nama_lengkap, u.photo,
             s.total_points, s.level, s.badge, s.breakdown'
        )
        ->from('gam_user_scores s')
        ->join('users u', 'u.id = s.user_id')
        ->where('s.total_points >', 0);

        if ($period_id) {
            $this->CI->db->where('s.period_id', (int)$period_id);
        } else {
            $this->CI->db->where('s.period_id IS NULL', null, false);
        }

        $rows = $this->CI->db
            ->order_by('s.total_points', 'DESC')
            ->limit($limit)
            ->get()->result();

        // Decode breakdown JSON and attach module score if filtering
        foreach ($rows as $row) {
            $row->breakdown_arr = $row->breakdown ? json_decode($row->breakdown, true) : [];
            $row->module_points = $module_code ? (isset($row->breakdown_arr[$module_code]) ? $row->breakdown_arr[$module_code] : 0) : null;
            
            // Get faq_approved count from breakdown
            $row->faq_approved = 0;
            if ($row->breakdown_arr && isset($row->breakdown_arr['faq'])) {
                $row->faq_approved = (int)$row->breakdown_arr['faq'];
            }
            
            // Get helpful_votes from breakdown
            $row->helpful_votes = 0;
            if ($row->breakdown_arr && isset($row->breakdown_arr['helpful_votes'])) {
                $row->helpful_votes = (int)$row->breakdown_arr['helpful_votes'];
            }
        }

        return $rows;
    }

    // =========================================================================
    // BADGES
    // =========================================================================

    /**
     * Evaluate all badge rules for a user after an award event.
     * Returns array of newly earned badge objects.
     */
    public function check_badges($user_id, $active_periods = [], $source_id = null) {
        $new_badges = [];
        $all_badges = $this->CI->db
            ->where('is_active', 1)
            ->order_by('sort_order', 'ASC')
            ->get('gam_badges')->result();

        foreach ($all_badges as $badge) {
            if ($badge->scope === 'lifetime') {
                if ($this->_already_earned($user_id, $badge->id, null)) continue;
                if ($this->_evaluate_badge($badge, $user_id, null, $source_id)) {
                    $this->_grant_badge($user_id, $badge->id, null);
                    $new_badges[] = $badge;
                }
            } elseif ($badge->scope === 'period') {
                foreach ($active_periods as $period) {
                    // Filter by rule_period_type if set
                    if ($badge->rule_period_type && $badge->rule_period_type !== $period->period_type) continue;
                    if ($this->_already_earned($user_id, $badge->id, $period->id)) continue;
                    if ($this->_evaluate_badge($badge, $user_id, $period->id, $source_id)) {
                        $this->_grant_badge($user_id, $badge->id, $period->id);
                        $new_badges[] = (object)array_merge(
                            (array)$badge,
                            ['period_label' => $period->label, 'period_id' => $period->id]
                        );
                    }
                }
            }
        }
        return $new_badges;
    }

    /**
     * Evaluate a single badge rule.
     * This is data-driven: rule_type determines how to check.
     */
    private function _evaluate_badge($badge, $user_id, $period_id, $source_id = null) {
        switch ($badge->rule_type) {

            case 'points_gte':
                $score = $this->get_user_score($user_id, $period_id);
                return $score && $score->total_points >= $badge->rule_threshold;

            case 'stat_gte':
                // Cross-module: sum all stats with this key; or module-specific
                if ($badge->rule_module) {
                    $val = $this->get_stat($user_id, $badge->rule_module, $badge->rule_stat_key, $period_id);
                } else {
                    // Sum across modules
                    $q = $this->CI->db->select('SUM(stat_value) as total')
                        ->where('user_id', (int)$user_id)
                        ->where('stat_key', $badge->rule_stat_key);
                    if ($period_id) {
                        $q->where('period_id', (int)$period_id);
                    } else {
                        $q->where('period_id IS NULL', null, false);
                    }
                    $row = $q->get('gam_user_module_stats')->row();
                    $val = $row ? (int)$row->total : 0;
                }
                return $val >= $badge->rule_threshold;

            case 'rank_lte':
                // User must be at or above rank N in this period
                $score = $this->get_user_score($user_id, $period_id);
                if (!$score || $score->total_points <= 0) return false;
                $rank = $this->CI->db
                    ->where($period_id ? 'period_id' : 'period_id IS NULL', $period_id ?: null, (bool)$period_id)
                    ->where('total_points >', $score->total_points)
                    ->count_all_results('gam_user_scores') + 1;
                return $rank <= $badge->rule_threshold;

            case 'manual':
                // Only awarded explicitly via grant_badge()
                return false;
        }
        return false;
    }

    /**
     * Manually grant a badge (for 'manual' rule_type or special events).
     */
    public function grant_badge($user_id, $badge_code, $period_id = null) {
        $badge = $this->CI->db->where('code', $badge_code)->get('gam_badges')->row();
        if (!$badge) return false;
        if ($this->_already_earned($user_id, $badge->id, $period_id)) return false;
        $this->_grant_badge($user_id, $badge->id, $period_id);
        return $badge;
    }

    // =========================================================================
    // PERIOD HELPERS
    // =========================================================================

    public function get_active_periods($date = null) {
        return $this->_get_active_periods($date ?: date('Y-m-d'));
    }

    public function get_period($period_id) {
        return $this->CI->db->where('id', (int)$period_id)->get('gam_periods')->row();
    }

    public function get_periods($type = null, $year = null) {
        $q = $this->CI->db;
        if ($type) $q->where('period_type', $type);
        if ($year) $q->where('year', (int)$year);
        return $q->order_by('year DESC, start_date DESC')->get('gam_periods')->result();
    }

    public function get_period_years() {
        // Get distinct years from gam_periods table
        $this->CI->db->select('year', FALSE);
        $this->CI->db->distinct();
        $this->CI->db->order_by('year', 'DESC');
        return $this->CI->db->get('gam_periods')->result();
    }

    // =========================================================================
    // LEVEL / XP HELPERS
    // =========================================================================

    public function calc_level($points) {
        $level = 1;
        foreach (self::$LEVELS as $lvl => $threshold) {
            if ($points >= $threshold) $level = $lvl;
        }
        return $level;
    }

    public function level_progress($points) {
        $level = $this->calc_level($points);
        if ($level >= 10) return 100;
        $curr = self::$LEVELS[$level];
        $next = self::$LEVELS[$level + 1];
        return round((($points - $curr) / ($next - $curr)) * 100);
    }

    public function get_levels()       { return self::$LEVELS; }
    public function get_level_titles() { return self::$LEVEL_TITLES; }

    // =========================================================================
    // MODULE REGISTRY HELPERS
    // =========================================================================

    public function get_modules() {
        return $this->CI->db->where('is_active', 1)->get('gam_modules')->result();
    }

    public function get_actions($module_code = null) {
        $q = $this->CI->db->select('a.*, m.name as module_name')
                          ->from('gam_actions a')
                          ->join('gam_modules m', 'm.id = a.module_id');
        if ($module_code) $q->where('m.code', $module_code);
        return $q->where('a.is_active', 1)->get()->result();
    }

    // =========================================================================
    // PRIVATE INTERNALS
    // =========================================================================

    /** Fetch and cache an action row by code */
    private function _get_action($code) {
        if (!isset($this->_action_cache[$code])) {
            $row = $this->CI->db
                ->select('a.*, m.code as module_code')
                ->from('gam_actions a')
                ->join('gam_modules m', 'm.id = a.module_id')
                ->where('a.code', $code)
                ->get()->row();
            $this->_action_cache[$code] = $row;
        }
        return $this->_action_cache[$code];
    }

    /** Fetch and cache active periods for a date */
    private function _get_active_periods($date) {
        if (!isset($this->_period_cache[$date])) {
            $this->_period_cache[$date] = $this->CI->db
                ->where('start_date <=', $date)
                ->where('end_date >=', $date)
                ->where('is_active', 1)
                ->get('gam_periods')->result();
        }
        return $this->_period_cache[$date];
    }

    /**
     * Resolve actual point value for an action in a set of periods.
     * Uses the override from the HIGHEST-priority (latest start_date) active period.
     */
    private function _resolve_points($action, $active_periods) {
        foreach ($active_periods as $period) {
            $override = $this->CI->db
                ->where(['period_id' => $period->id, 'action_id' => $action->id])
                ->get('gam_period_action_overrides')->row();
            if ($override) return (int)$override->point_value;
        }
        return (int)$action->point_value;
    }

    /**
     * Upsert gam_user_scores.
     * Returns the user's level BEFORE this update (for level-up detection).
     */
    private function _upsert_score($user_id, $period_id, $pts, $module_code) {
        // Ensure row exists
        $q = $this->CI->db->where('user_id', (int)$user_id);
        if ($period_id) {
            $q->where('period_id', (int)$period_id);
        } else {
            $q->where('period_id IS NULL', null, false);
        }
        $existing = $q->get('gam_user_scores')->row();
        $prev_level = $existing ? (int)$existing->level : 1;

        if (!$existing) {
            $this->CI->db->insert('gam_user_scores', [
                'user_id'      => (int)$user_id,
                'period_id'    => $period_id,
                'total_points' => 0,
                'level'        => 1,
                'badge'        => self::$LEVEL_TITLES[1],
            ]);
        }

        // Update total_points (floor at 0)
        $this->CI->db->query(
            "UPDATE `gam_user_scores`
             SET `total_points` = GREATEST(0, `total_points` + ?),
                 `updated_at`   = NOW()
             WHERE `user_id` = ? AND " .
            ($period_id ? "`period_id` = ?" : "`period_id` IS NULL"),
            $period_id ? [$pts, (int)$user_id, (int)$period_id] : [$pts, (int)$user_id]
        );

        // Re-read points to compute new level
        $q2 = $this->CI->db->select('total_points, breakdown')->where('user_id', (int)$user_id);
        if ($period_id) {
            $q2->where('period_id', (int)$period_id);
        } else {
            $q2->where('period_id IS NULL', null, false);
        }
        $fresh = $q2->get('gam_user_scores')->row();

        if ($fresh) {
            $new_pts   = (int)$fresh->total_points;
            $new_level = $this->calc_level($new_pts);
            $new_badge = self::$LEVEL_TITLES[$new_level];

            // Update module breakdown JSON
            $breakdown = $fresh->breakdown ? json_decode($fresh->breakdown, true) : [];
            $breakdown[$module_code] = (isset($breakdown[$module_code]) ? $breakdown[$module_code] : 0) + $pts;
            if ($breakdown[$module_code] < 0) $breakdown[$module_code] = 0;

            $this->CI->db->query(
                "UPDATE `gam_user_scores`
                 SET `level` = ?, `badge` = ?, `breakdown` = ?
                 WHERE `user_id` = ? AND " .
                ($period_id ? "`period_id` = ?" : "`period_id` IS NULL"),
                $period_id
                    ? [$new_level, $new_badge, json_encode($breakdown), (int)$user_id, (int)$period_id]
                    : [$new_level, $new_badge, json_encode($breakdown), (int)$user_id]
            );
        }

        return $prev_level;
    }

    /** Upsert a module stat counter */
    private function _increment_stat($user_id, $module_code, $period_id, $stat_key, $delta = 1) {
        $exists = $this->CI->db->where([
            'user_id'     => (int)$user_id,
            'module_code' => $module_code,
            'stat_key'    => $stat_key,
        ]);
        if ($period_id) {
            $exists->where('period_id', (int)$period_id);
        } else {
            $exists->where('period_id IS NULL', null, false);
        }
        $row = $exists->get('gam_user_module_stats')->row();

        if ($row) {
            $this->CI->db->query(
                "UPDATE `gam_user_module_stats`
                 SET `stat_value` = GREATEST(0, `stat_value` + ?), `updated_at` = NOW()
                 WHERE `user_id` = ? AND `module_code` = ? AND `stat_key` = ? AND " .
                ($period_id ? "`period_id` = ?" : "`period_id` IS NULL"),
                $period_id
                    ? [$delta, (int)$user_id, $module_code, $stat_key, (int)$period_id]
                    : [$delta, (int)$user_id, $module_code, $stat_key]
            );
        } else {
            $this->CI->db->insert('gam_user_module_stats', [
                'user_id'     => (int)$user_id,
                'module_code' => $module_code,
                'period_id'   => $period_id,
                'stat_key'    => $stat_key,
                'stat_value'  => max(0, $delta),
            ]);
        }
    }

    private function _already_earned($user_id, $badge_id, $period_id) {
        $q = $this->CI->db->where(['user_id' => (int)$user_id, 'badge_id' => (int)$badge_id]);
        if ($period_id) {
            $q->where('period_id', (int)$period_id);
        } else {
            $q->where('period_id IS NULL', null, false);
        }
        return $q->count_all_results('gam_user_badges') > 0;
    }

    private function _grant_badge($user_id, $badge_id, $period_id) {
        $this->CI->db->insert('gam_user_badges', [
            'user_id'   => (int)$user_id,
            'badge_id'  => (int)$badge_id,
            'period_id' => $period_id,
            'earned_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
