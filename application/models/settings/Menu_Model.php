<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_Model extends CI_Model
{

    // ---------------------------------------------------------------
    // Runtime — used by MY_Controller to build nav
    // ---------------------------------------------------------------

	/**
	 * Returns a nested tree of menu items the user is allowed to see.
	 * Results can be cached in session since permissions rarely change mid-session.
	 */
	public function get_menu_for_user($user_id)
	{
		// Fetch allowed slugs for this user via their groups
		// Use direct DB query instead of Ion Auth's get_users_groups (PHP 5.6 compatibility issue)
		$groups = $this->db->select('group_id')
			->where('user_id', $user_id)
			->get('users_groups')
			->result_array();

		$group_ids     = array_column($groups, 'group_id');
		$allowed_slugs = [];

		if (! empty($group_ids)) {
			$rows = $this->db
				->select('p.slug')
				->from('permissions p')
				->join('group_permissions gp', 'gp.permission_id = p.id')
				->where_in('gp.group_id', $group_ids)
				->get()
				->result_array();

			$allowed_slugs = array_column($rows, 'slug');
		}

		// All active items ordered (with permission info)
		$all = $this->db
			->select('menus.*, permissions.slug as permission_slug')
			->from('menus')
			->join('permissions', 'menus.permission_id = permissions.id', 'left')
			->where('menus.status', 1)
			->order_by('menus.sort_order ASC')
			->get()
			->result_array();

		// Filter: keep items whose permission_slug is allowed (or NULL)
		$visible = array_filter($all, function ($item) use ($allowed_slugs) {
			// Keep items with no permission (public) OR items with permission in allowed list
			return empty($item['permission_slug'])
				|| in_array($item['permission_slug'], $allowed_slugs);
		});

		return $this->build_tree($visible);
	}

	/**
	 * Build tree from flat list
	 * @param array $items Flat menu items
	 * @param mixed $parent_id Parent ID to build tree for
	 * @param bool $filter_empty Whether to filter out empty menus (no URL, no children)
	 * @return array Tree structure
	 */
	private function build_tree(array $items, $parent_id = NULL, $filter_empty = FALSE)
	{
		$tree = [];
		foreach ($items as $item) {
			if ($item['parent_id'] == $parent_id) {
				$children = $this->build_tree($items, $item['id'], $filter_empty);
				
				// Only filter if explicitly requested
				if ($filter_empty && empty($item['url']) && empty($children)) {
					continue;
				}
				
				$item['children'] = $children;
				$tree[] = $item;
			}
		}
		return $tree;
	}

	// ---------------------------------------------------------------
	// Manager UI — CRUD
	// ---------------------------------------------------------------

	public function get_all_flat()
	{
		$query = $this->db
			->order_by('sort_order ASC')
			->get('menus');
		// write_custom_log($this->db->last_query(), 'info', 'query');
		return $query->result_array();
	}

	/**
	 * Get ALL menus for admin management (including empty/placeholder menus)
	 * @return array Tree structure with ALL menus
	 */
	public function get_all_tree()
	{
		$all = $this->get_all_flat();
		return $this->build_tree($all, NULL, FALSE);  // Don't filter - show all for admin
	}

	/**
	 * Get navigation tree for front-end preview (filter out inactive menus only)
	 * @return array Tree structure with only active menus
	 */
	public function get_navigation_tree()
	{
		$all = $this->get_all_flat();
		// Filter: keep only active items
		$active = array_filter($all, function ($item) {
			return $item['status'] == 1;
		});
		return $this->build_tree($active, NULL, FALSE);  // Don't filter empty menus
	}

	public function get_by_id($id)
	{
		return $this->db->where('id', $id)->get('menus')->row_array();
	}

	public function create(array $data)
	{
		$this->db->insert('menus', $this->_sanitize($data));
		return $this->db->insert_id();
	}

	public function update($id, array $data)
	{
		return $this->db->where('id', $id)->update('menus', $this->_sanitize($data));
	}

	public function delete($id)
	{
		// Re-parent children to deleted item's parent
		$item = $this->get_by_id($id);
		if ($item) {
			$data = array('parent_id' => $item['parent_id']);
			$this->db->where('parent_id', $id)
				->update('menus', $data);
		}
		return $this->db->where('id', $id)->delete('menus');
	}

	/**
	 * Bulk update sort_order + parent_id from a flat ordered array.
	 * Called after drag-and-drop reorder.
	 *
	 * $order = [
	 *   ['id' => 3, 'parent_id' => null, 'sort_order' => 10],
	 *   ['id' => 5, 'parent_id' => 3,    'sort_order' => 10],
	 * ]
	 */
	public function reorder(array $order)
	{
		foreach ($order as $row) {
			$data = array(
				'parent_id'  => isset($row['parent_id']) ? (int) $row['parent_id'] : NULL,
				'sort_order' => (int) $row['sort_order'],
			);
			$this->db->where('id', (int) $row['id'])->update('menus', $data);
		}
		return true;
	}

	public function toggle_active($id)
	{
		$item = $this->get_by_id($id);
		if (!$item) return false;

		$data = array('status' => $item['status'] ? 0 : 1);
		$this->db->where('id', $id)->update('menus', $data);
		return true;
	}

	// ---------------------------------------------------------------

	private function _sanitize(array $data)
	{
		return array(
			'parent_id'       => isset($data['parent_id']) && $data['parent_id'] !== '' ? (int) $data['parent_id'] : NULL,
			'label'           => trim($data['label']),
			'url'             => isset($data['url']) && $data['url'] !== '' ? trim($data['url']) : NULL,
			'icon'            => isset($data['icon']) ? trim($data['icon']) : NULL,
			'permission_id'   => isset($data['permission_id']) && $data['permission_id'] !== '' ? (int) $data['permission_id'] : NULL,
			'sort_order'      => isset($data['sort_order']) ? (int) $data['sort_order'] : 0,
			'status'          => isset($data['status']) ? (int) $data['status'] : 1,
		);
	}
}
