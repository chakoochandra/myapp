<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Model extends CI_Model
{
	protected $tableName;
	protected $tableViewsName;
	protected $colSearch = [];
	protected $colOrder = [];
	protected $sipp_is_defined = false;
	protected $database;
	protected $db_sipp;
	protected $db_siadpa;

	protected $primaryIdField = 'id';
	protected $fileFolders = [];

	public function __construct()
	{
		parent::__construct();

		// Check if db_sipp configuration exists by loading the config file
		$db_config = array();
		if (file_exists(APPPATH . 'config/' . ENVIRONMENT . '/database.php')) {
			include(APPPATH . 'config/' . ENVIRONMENT . '/database.php');
			if (isset($db)) {
				$db_config = $db;
			}
		} elseif (file_exists(APPPATH . 'config/database.php')) {
			include(APPPATH . 'config/database.php');
			if (isset($db)) {
				$db_config = $db;
			}
		}

		// Check if db_sipp configuration exists and has hostname
		if (isset($db_config['db_sipp']) && !empty($db_config['db_sipp']['hostname'])) {
			$this->sipp_is_defined = true;
		}

		$this->database = $this->db;
	}

	function findOne($id, $canNull = false)
	{
		$this->database->select("$this->tableName.*");
		$this->database->from($this->tableName);
		$this->database->where(["$this->tableName.$this->primaryIdField" => $id]);

		$query = $this->database->get();
		// write_custom_log($this->database->last_query());
		if (($row = $query->row())) {
			$this->prepare_files($row);
			return $row;
		} else if ($canNull) {
			return false;
		}

		$this->session->set_flashdata('error_message', 'Data tidak ditemukan #' . $id);
		return redirect('site/error');
	}

	function findOneWhere($where)
	{
		$this->database->from($this->tableName);
		$this->database->where($where);
		return $this->database->get()->row();
	}

	function find($where = [], $offset = null, $limit = null)
	{
		$this->database->from($this->tableName);

		$this->populateWhere($where);

		if ($limit) {
			$this->database->limit($limit);
		}

		if ($offset) {
			$this->database->offset($offset);
		}

		$this->global_order();

		return $this->database->get()->result();
	}

	function get_list($where = [])
	{
		$this->list_query($where);
		if ($this->input->post('length') != -1) {
			$this->database->limit($this->input->post('length'), $this->input->post('start'));
		}

		$query = $this->database->get();

		if (is_development()) {
			write_custom_log($this->database->last_query());
		}

		$this->database->reset_query();
		$result = $query->result();
		if (!empty($result)) {
			foreach ($result as $row) {
				$this->prepare_files($row);
			}
		}

		return $result;
	}

	function count_list_filtered($where = [])
	{
		$this->list_query($where);

		$count = $this->database->get()->num_rows();

		$this->database->reset_query();

		return $count;
	}

	function count_list_all($where = [])
	{
		$this->list_query($where, false);

		$count = $this->database->count_all_results();

		$this->database->reset_query();

		return $count;
	}

	/**
	 * to be overriden in child model
	 */
	protected function list_query($where, $do_filter = true)
	{
		if ($where) {
			$this->database->where($where);
		}

		// Handling global search
		$this->global_filter($do_filter);

		// Handling column-specific search
		$this->filter_by_column($do_filter);

		// Handling ordering
		$this->global_order();
	}

	/**
	 * Prepare file metadata (size and URL) for a row
	 * 
	 * @param object $row Database row object (modified by reference)
	 */
	protected function prepare_files(&$row)
	{
		if (!$row) return;

		// Add file size and URL dynamically for all file fields
		foreach (array_keys($this->fileFolders) as $field) {
			$fileName = isset($row->$field) ? $row->$field : null;

			if ($fileName) {
				$folder = $this->fileFolders[$field];
				// Normalize folder root path (remove leading ./ and trailing /)
				$upload_root = rtrim(ltrim($this->config->item('folder_root_upload'), './'), '/');
				$file_path = FCPATH . $upload_root . '/' . $folder . '/' . $fileName;

				if (file_exists($file_path)) {
					$file_size = filesize($file_path);
					$row->{$field . '_size'} = $file_size;
					$row->{$field . '_url'} = file_url($folder, $fileName);
				} else {
					$row->{$field . '_size'} = 0;
					$row->{$field . '_url'} = null;
				}
			} else {
				// No file assigned
				$row->{$field . '_size'} = 0;
				$row->{$field . '_url'} = null;
			}
		}
	}

	protected function global_order()
	{
		if ($this->input->post('order')['0']['name']) {
			foreach ($this->input->post('order') as $i => $order) {
				$this->database->order_by($order['name'], $order['dir']);
			}
		} else {
			foreach ($this->colOrder as $column => $direction) {
				$this->database->order_by($column, $direction);
			}
		}
	}

	protected function global_filter($do_filter = true)
	{
		if ($do_filter && $this->input->post('search') && isset($this->input->post('search')['value']) && $this->input->post('search')['value']) {
			$i = 0;
			foreach ($this->colSearch as $item) {
				if ($i === 0) {
					$this->database->group_start();
					$this->database->like($item, $this->input->post('search')['value']);
				} else {
					$this->database->or_like($item, $this->input->post('search')['value']);
				}
				if (count($this->colSearch) - 1 == $i)
					$this->database->group_end();
				$i++;
			}
		}
	}

	protected function filter_by_column($do_filter = true)
	{
		// Handling column-specific search
		$columnFilters = $this->input->post('columns');
		if ($do_filter && $columnFilters) {
			foreach ($columnFilters as $index => $column) {
				if (!empty($column['search']['value'])) {
					$this->database->where($column['data'], $column['search']['value']);
				}
			}
		}
	}

	protected function populateWhere($where)
	{
		foreach ($where as $key => $q) {
			switch ($key) {
				case 'like':
					if (is_array($q) && !empty($q)) {
						for ($i = 0; $i < count($q); $i++) {
							if ($i == 0) {
								$this->database->like($q[$i]);
							} else {
								$this->database->or_like($q[$i]);
							}
						}
					}
					break;
				default:
					if (is_array($q) && !empty($q)) {
						foreach ($q as $w) {
							$isWhereIn = false;
							if (is_array($w)) {
								foreach ($w as $key => $z) {
									if (is_array($z)) {
										$isWhereIn = true;
										$this->database->where_in($key, $z);
									}
								}
							}

							if (!$isWhereIn) {
								$this->database->where($w);
							}
						}
					}
					break;
			}
		}
	}

	function num_rows($where = [])
	{
		$this->database->from($this->tableName);

		$this->populateWhere($where);

		return $this->database->get()->num_rows();
	}
}

class Crud_Model extends MY_Model
{
	use Aggregate_trait;

	function insert($data)
	{
		$this->database->trans_start();

		$this->database->insert($this->tableName, $data);

		$insert_id = $this->database->insert_id();

		$this->database->trans_complete();

		return $insert_id;
	}

	function update($id, $data)
	{
		return $this->database->where($this->primaryIdField, $id)->update($this->tableName, $data);
	}

	function update_batch($data, $key = 'id')
	{
		return $this->database->update_batch($this->tableName, $data, $key);
	}

	function update_column($ids, $value, $colName)
	{
		if (is_array($ids)) {
			foreach ($ids as $id) {
				$data = [
					$this->primaryIdField => $id,
					$colName => $value
				];

				if (($query = $this->database->where($this->primaryIdField, $id)->get($this->tableName)) && $query->num_rows() > 0) {
					$this->database->where($this->primaryIdField, $id)->update($this->tableName, $data);
				} else {
					$this->database->insert($this->tableName, $data);
				}
			}
		} else {
			$data = [
				$this->primaryIdField => $ids,
				$colName => $value
			];

			if (($query = $this->database->where($this->primaryIdField, $ids)->get($this->tableName)) && $query->num_rows() > 0) {
				$this->database->where($this->primaryIdField, $ids);
				$this->database->update($this->tableName, $data);
			} else {
				$this->database->insert($this->tableName, $data);
			}
		}
	}

	function delete($id)
	{
		$this->database->delete($this->tableName, array($this->primaryIdField => $id));
		return $this->database->affected_rows() > 0;
	}

	/**
	 * Get distinct values for any column from the database
	 */
	public function getDistinctColumnValues($column, $searchTerm = null)
	{
		// Validate that the column exists in the table to prevent SQL injection
		$fields = $this->database->list_fields($this->tableName);
		if (!in_array($column, $fields)) {
			return [];
		}

		$this->database->select("{$column}, COUNT(*) as frequency", false);
		$this->database->from($this->tableName);
		$this->database->where("{$column} IS NOT NULL");
		$this->database->where("{$column} !=", '');

		// Add search condition if search term is provided
		if (!empty($searchTerm)) {
			$this->database->like($column, $searchTerm, 'both');
		}

		$this->database->group_by($column);
		$this->database->order_by('frequency', 'DESC');
		$this->database->limit(5);

		// Format the results for jQuery UI Autocomplete
		$result = [];
		foreach ($this->database->get()->result() as $value) {
			$valueData = $value->$column;
			if (!empty($valueData)) {
				$result[] = [
					'label' => $valueData,
					'value' => $valueData
				];
			}
		}

		return $result;
	}

	public function incrementView($document_id, $user_id)
	{
		$this->database->insert($this->tableViewsName, [
			'document_id' => $document_id,
			'user_id' => $user_id,
			'viewed_at' => date('Y-m-d H:i:s')
		]);
	}

	public function getDocumentViewCount($document_id)
	{
		$this->database->where('document_id', $document_id);
		return $this->database->count_all_results($this->tableViewsName);
	}

	protected function get_pegawai_order()
	{
		return array(
			$this->config->item('tbl_ref_jabatan') . '.urutan' => 'asc',
			'(CASE WHEN ' . $this->config->item('tbl_ref_golongan') . '.UrutanGolonganRuang IS NULL THEN 1 ELSE 0 END)' => 'asc',
			$this->config->item('tbl_ref_golongan') . '.UrutanGolonganRuang' => 'asc',
			'masa_pangkat_tahun' => 'desc',
			'masa_pangkat_bulan' => 'desc',
			'masa_kerja_tahun' => 'desc',
			'masa_kerja_bulan' => 'desc',
			'(CASE WHEN u.start_date IS NULL THEN 1 ELSE 0 END)' => 'asc',
			'u.start_date' => 'asc',
			'SUBSTRING(u.nip, 9, 6)' => null,
			'u.nip' => 'asc',
		);
	}
}

class SippBase_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->db_sipp = $this->load->database('db_sipp', TRUE);
		$this->database = $this->db_sipp;
	}
}

class SiadpaBase_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->db_siadpa = $this->load->database('db_siadpa', TRUE);
		$this->database = $this->db_siadpa;
	}
}

class ApsBase_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->db_aps = $this->load->database('db_aps', TRUE);
		$this->database = $this->db_aps;
	}
}

trait Aggregate_trait
{
	/**
	 * to be overriden in child model
	 * Should build the query using $this->database
	 */
	protected function statistic_query()
	{
		// Default implementation - select from table if it exists
		if (isset($this->tableName) && !empty($this->tableName)) {
			$this->database->from($this->tableName);
		}
	}

	function get_statistic()
	{
		// Build the query
		$this->statistic_query();

		// Apply pagination if available
		if (method_exists($this, 'input') && $this->input) {
			$length = $this->input->post('length');
			$start = $this->input->post('start');

			if ($length != -1 && $length !== null) {
				$this->database->limit($length, $start ? $start : 0);
			}
		}

		$query = $this->database->get();
		if (is_development()) {
			write_custom_log($this->database->last_query());
		}
		$this->database->reset_query();

		return $query->result();
	}

	function count_statistic_filtered()
	{
		$this->statistic_query();
		return $this->database->get()->num_rows();
	}

	function count_statistic_all()
	{
		$this->statistic_query(false);
		return $this->database->count_all_results();
	}
}
class SippAggregate_Model extends SippBase_Model
{
	use Aggregate_trait;
}
