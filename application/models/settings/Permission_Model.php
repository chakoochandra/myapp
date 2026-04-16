<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission_Model extends CI_Model {

    // ---------------------------------------------------------------
    // Auth checks (used by MY_Controller)
    // ---------------------------------------------------------------

    public function user_has_permission($user_id, $slug)
    {
        $groups = $this->db->select('group_id')
            ->where('user_id', $user_id)
            ->get('users_groups')
            ->result_array();

        $group_ids = array_column($groups, 'group_id');
        if (empty($group_ids)) return false;

        return (bool) $this->db
            ->from('permissions p')
            ->join('group_permissions gp', 'gp.permission_id = p.id')
            ->where('p.slug', $slug)
            ->where_in('gp.group_id', $group_ids)
            ->get()
            ->row();
    }

    public function user_owns_resource($user_id, $resource_type, $resource_id)
    {
        return (bool) $this->db->where([
            'user_id'       => $user_id,
            'resource_type' => $resource_type,
            'resource_id'   => $resource_id,
        ])->get('resource_ownership')->row();
    }

    // ---------------------------------------------------------------
    // UI — groups
    // ---------------------------------------------------------------

    public function get_all_groups()
    {
        return $this->db->get('groups')->result_array();
    }

    // ---------------------------------------------------------------
    // UI — permissions
    // ---------------------------------------------------------------

    public function get_permissions_grouped_by_category()
    {
        $rows = $this->db
            ->order_by('category, label')
            ->get('permissions')
            ->result_array();

        $grouped = [];
        foreach ($rows as $row) {
            $cat = $row['category'] ?: 'General';
            $grouped[$cat][] = $row;
        }

        return $grouped;
    }

    public function get_group_permission_ids($group_id)
    {
        $rows = $this->db
            ->select('permission_id')
            ->where('group_id', $group_id)
            ->get('group_permissions')
            ->result_array();

        return array_column($rows, 'permission_id');
    }

    public function sync_group_permissions($group_id, array $permission_ids)
    {
        $this->db->where('group_id', $group_id)->delete('group_permissions');

        if ( ! empty($permission_ids)) {
            $rows = array();
            foreach ($permission_ids as $pid) {
                $rows[] = array(
                    'group_id'      => $group_id,
                    'permission_id' => $pid,
                );
            }

            $this->db->insert_batch('group_permissions', $rows);
        }

        return true;
    }

    public function create_permission($slug, $label, $description, $category)
    {
        $this->db->insert('permissions', [
            'slug'        => $slug,
            'label'       => $label,
            'description' => $description,
            'category'    => $category,
        ]);
        return $this->db->insert_id();
    }

    public function delete_permission($id)
    {
        $this->db->where('permission_id', $id)->delete('group_permissions');
        $this->db->where('id', $id)->delete('permissions');
    }

    public function update_permission($id, $label, $description, $category)
    {
        $this->db->where('id', $id)->update('permissions', [
            'label'       => $label,
            'description' => $description,
            'category'    => $category,
        ]);
    }

    // ---------------------------------------------------------------
    // Ownership helpers
    // ---------------------------------------------------------------

    public function assign_ownership($user_id, $resource_type, $resource_id)
    {
        $exists = $this->db->where([
            'user_id'       => $user_id,
            'resource_type' => $resource_type,
            'resource_id'   => $resource_id,
        ])->get('resource_ownership')->row();

        if ( ! $exists) {
            $this->db->insert('resource_ownership', [
                'user_id'       => $user_id,
                'resource_type' => $resource_type,
                'resource_id'   => $resource_id,
            ]);
        }
    }

    public function revoke_ownership($resource_type, $resource_id)
    {
        $this->db->where([
            'resource_type' => $resource_type,
            'resource_id'   => $resource_id,
        ])->delete('resource_ownership');
    }
}
