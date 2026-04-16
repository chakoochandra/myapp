<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Log_Model extends Crud_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->tableName = $this->config->item('tbl_whatsapp');
		$this->colSearch = array('phone_number', 'text');
		$this->colOrder = ['sent_time' => 'DESC'];
	}

	protected function list_query($where, $do_filter = true)
	{
		$this->db->select([
			$this->tableName . '.*',
		]);
		$this->db->from($this->tableName);

		// Add filter for type if provided
		$type_filter = $this->input->post('type_filter') ?: $this->input->get('type_filter');
		if ($type_filter && $type_filter !== '') {
			$this->db->where($this->tableName . '.type', $type_filter);
		}

		parent::list_query($where, $do_filter);
	}

	/**
	 * Get distinct values from the 'type' column
	 */
	public function get_distinct_types()
	{
		$this->db->distinct();
		$this->db->select('type');
		$this->db->from($this->tableName);
		$this->db->where('type IS NOT NULL');
		$this->db->order_by('type', 'ASC');
		
		$query = $this->db->get();
		return $query->result();
	}
}
