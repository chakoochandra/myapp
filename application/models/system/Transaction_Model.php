<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Transaction_Model extends Crud_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->tableName = 'trans_minutation';
	}

	function update_column($ids, $date, $colName)
	{
		if (is_array($ids)) {
			foreach ($ids as $id) {
				$parts = explode('_', $id, 2);
				$realId = $parts[0];
				$dynamicColName = isset($parts[1]) ? $colName . '_' . $parts[1] : $colName;

				$data = [
					'perkara_id' => $realId,
					$dynamicColName => $date
				];

				if (($query = $this->db->where('perkara_id', $realId)->get($this->tableName)) && $query->num_rows() > 0) {
					$this->db->where('perkara_id', $realId)->update($this->tableName, $data);
				} else {
					$this->db->insert($this->tableName, $data);
				}
			}
		} else {
			$parts = explode('_', $ids, 2);
			$realId = $parts[0];
			$dynamicColName = isset($parts[1]) ? $colName . '_' . $parts[1] : $colName;

			$data = [
				'perkara_id' => $realId,
				$dynamicColName => $date
			];

			if (($query = $this->db->where('perkara_id', $realId)->get($this->tableName)) && $query->num_rows() > 0) {
				$this->db->where('perkara_id', $realId)->update($this->tableName, $data);
			} else {
				$this->db->insert($this->tableName, $data);
			}
		}
	}
}
