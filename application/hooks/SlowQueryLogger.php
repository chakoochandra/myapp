<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SlowQueryLogger
{
    /**
     * Post-controller hook: logs queries exceeding the threshold and adds a Server-Timing header.
     * Threshold (ms) resolution order:
     *  - $this->config['slow_query_threshold_ms']
     *  - $_SERVER['SLOW_QUERY_THRESHOLD_MS']
     *  - default 100 ms
     * 
     * Exclusions can be configured in config file:
     *  $config['slow_query_exclude_controllers'] = ['welcome', 'test'];
     *  $config['slow_query_exclude_methods'] = [
     *      'users' => ['index', 'view'],
     *      'api' => '*'  // exclude all methods
     *  ];
     */
    public function log()
    {
        $CI =& get_instance();

        // Ensure config exists
        if (!isset($CI->config) || !is_object($CI->config)) {
            return;
        }

        // Check exclusions
        if ($this->shouldExclude($CI)) {
            return;
        }

        // Skip if DB not loaded or driver doesn't expose timing
        if (!isset($CI->db) || !is_object($CI->db)) {
            return;
        }

        // Determine threshold (milliseconds)
        $threshold = 100;
        $cfg = $CI->config->item('slow_query_threshold_ms');
        if (is_numeric($cfg) && $cfg > 0) {
            $threshold = (int) $cfg;
        }
        if (isset($_SERVER['SLOW_QUERY_THRESHOLD_MS']) && is_numeric($_SERVER['SLOW_QUERY_THRESHOLD_MS'])) {
            $threshold = (int) $_SERVER['SLOW_QUERY_THRESHOLD_MS'];
        }

        // Choose log level based on current log_threshold (default 1)
        $logThreshold = (int) $CI->config->item('log_threshold');
        // CI levels: error (1), debug (2), info (3), all (4)
        // $level = ($logThreshold >= 2) ? 'debug' : 'error';
        $level = 'info';

        // Gather queries and times
        $queries = isset($CI->db->queries) ? $CI->db->queries : [];
        $times   = isset($CI->db->query_times) ? $CI->db->query_times : [];

        $count = min(count($queries), count($times));
        if ($count === 0) {
            return;
        }

        $slowCount = 0;
        $totalMs = 0.0;

        for ($i = 0; $i < $count; $i++) {
            $timeMs = round(($times[$i] ?: 0) * 1000, 2);
            $totalMs += $timeMs;

            if ($timeMs >= $threshold) {
                $slowCount++;
                $sql = preg_replace('/\s+/', ' ', (string) $queries[$i]);

                $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
                $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';

                log_message(
                    $level,
                    sprintf('[SlowQuery] %sms | %s %s | %s', $timeMs, $method, $uri, $sql)
                );
            }
        }

        // Attach Server-Timing header (total DB time)
        if (isset($CI->output) && is_object($CI->output) && !headers_sent()) {
            $serverTiming = sprintf('db;dur=%.2f', $totalMs);
            // Include a note with threshold/slow count
            $serverTiming .= sprintf(', slowq;desc="%d queries >= %dms"', $slowCount, $threshold);
            // Append header (do not overwrite if already present)
            header('Server-Timing: ' . $serverTiming, false);
        }

        // Summary line
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
        log_message($level, sprintf('[DBSummary] %s %s | queries=%d, total=%.2fms, slow(>=%dms)=%d', $method, $uri, $count, $totalMs, $threshold, $slowCount));
    }

    /**
     * Check if current controller/method should be excluded from logging
     *
     * @param object $CI CodeIgniter instance
     * @return bool True if should be excluded, false otherwise
     */
    private function shouldExclude(&$CI)
    {
        // Get current controller and method
        if (!isset($CI->router) || !is_object($CI->router)) {
            return false;
        }

        $controller = strtolower($CI->router->fetch_class());
        $method = strtolower($CI->router->fetch_method());

        // Check for controller-level exclusions
        $excludedControllers = $CI->config->item('slow_query_exclude_controllers');
        if (is_array($excludedControllers)) {
            $excludedControllers = array_map('strtolower', $excludedControllers);
            if (in_array($controller, $excludedControllers)) {
                return true;
            }
        }

        // Check for method-level exclusions
        $excludedMethods = $CI->config->item('slow_query_exclude_methods');
        if (is_array($excludedMethods)) {
            // Normalize keys to lowercase
            $excludedMethods = array_change_key_case($excludedMethods, CASE_LOWER);
            
            if (isset($excludedMethods[$controller])) {
                // If '*' is set, exclude all methods for this controller
                if ($excludedMethods[$controller] === '*') {
                    return true;
                }
                
                // Check if current method is in the exclusion list
                if (is_array($excludedMethods[$controller])) {
                    $methods = array_map('strtolower', $excludedMethods[$controller]);
                    if (in_array($method, $methods)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}