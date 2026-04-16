<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bht_Model extends SippBase_Model
{
	private $_tblMinutasi;

	public function __construct()
	{
		parent::__construct();
		$this->_tblMinutasi = $this->db->database . '.trans_minutation';
	}

	protected function list_query($where, $do_filter = true)
	{
		$selectedYear = $this->input->post('selectedYear') ?: date('Y');
		$selectedMonth = $this->input->post('selectedMonth') ?: null;
		$selectedDateBHT = $this->input->post('selectedDateBHT') ?: null;
		$selectedDateRencanaBHT = $this->input->post('selectedDateRencanaBHT') ?: null;
		$selectedBht = $this->input->post('selectedBht') ?: null;
		$selectedAc = $this->input->post('selectedAc') ?: null;

		$this->colSearch = array(
			'vp.nomor_perkara',
			'vp.majelis_hakim_nama',
			'vp.panitera_pengganti_text',
		);

		$this->colOrder = array(
			'(CASE WHEN b.tgl_akta_cerai IS NULL THEN 0 ELSE 1 END)' => 'asc',
			'(CASE WHEN vp.tanggal_bht IS NULL THEN 0 ELSE 1 END)' => 'asc',
			'vp.tanggal_bht' => 'asc',
			'(CASE WHEN tm.tanggal_rencana_bht IS NOT NULL THEN 0 ELSE 1 END)' => 'asc',
			'tm.tanggal_rencana_bht' => 'asc',
			'(CASE WHEN vp.tanggal_putusan IS NULL THEN 0 ELSE 1 END)' => 'desc',
			'vp.tanggal_putusan' => 'asc',
			'vp.perkara_id' => 'asc',
		);

		$this->database->select([
			'vp.perkara_id AS row_id',
			'vp.tanggal_putusan',
			'tm.tanggal_rencana_bht',
			'vp.nomor_perkara',
			'vp.majelis_hakim_nama AS hakim_nama',
			'vp.panitera_pengganti_text AS panitera_nama',
			'vp.tanggal_bht',
			'b.tgl_akta_cerai',
		]);

		$this->database->from('v_perkara vp');
		$this->database->join('perkara_akta_cerai b', 'vp.perkara_id = b.perkara_id', 'left');
		$this->database->join($this->_tblMinutasi . ' tm', 'vp.perkara_id = tm.perkara_id', 'left');

		$this->database->where('vp.tanggal_putusan IS NOT NULL');

		if ($selectedYear) {
			$this->database->where('YEAR(vp.tanggal_putusan)', $selectedYear);
		}
		if ($selectedMonth) {
			$this->database->where("DATE_FORMAT(vp.tanggal_putusan, '%Y-%m') = ", $selectedMonth);
		}
		if ($selectedDateBHT) {
			$this->database->where('DATE(vp.tanggal_bht)', $selectedDateBHT);
		}
		if ($selectedDateRencanaBHT) {
			$this->database->where('DATE(tm.tanggal_rencana_bht)', $selectedDateRencanaBHT);
		}
		if ($selectedAc) {
			$this->database->where_in('vp.jenis_perkara_id', [346, 347]);
			$this->database->where($selectedAc == 1 ? 'b.tgl_akta_cerai IS NOT NULL' : 'b.tgl_akta_cerai IS NULL');
		}
		if ($selectedBht) {
			$this->database->where($selectedBht == 1 ? 'vp.tanggal_bht IS NOT NULL' : 'vp.tanggal_bht IS NULL');
		}

		parent::list_query($where, $do_filter);
	}

	function getRencanaBhtByDate($today, $tomorrow, $dayAfterTomorrow)
	{
		$db = $this->database;

		$db->select([
			'tm.tanggal_rencana_bht',
			'COUNT(*) AS jumlah',
		]);
		$db->from('v_perkara vp');
		$db->join($this->_tblMinutasi . ' tm', 'vp.perkara_id = tm.perkara_id', 'left');
		$db->where('tm.tanggal_rencana_bht IS NOT NULL', null, false);
		$db->where_in('tm.tanggal_rencana_bht', [$today, $tomorrow, $dayAfterTomorrow]);
		$db->where('vp.tanggal_bht', null);
		$db->group_by('tm.tanggal_rencana_bht');
		$db->order_by('tm.tanggal_rencana_bht', 'asc');

		$query = $db->get();
		$result = $query->result();

		if (!empty($result)) {
			foreach ($result as $row) {
				$db->reset_query();
				$db->select('vp.nomor_perkara');
				$db->from('v_perkara vp');
				$db->join($this->_tblMinutasi . ' tm', 'vp.perkara_id = tm.perkara_id', 'left');
				$db->where('tm.tanggal_rencana_bht', $row->tanggal_rencana_bht);
				$db->where('vp.tanggal_bht', null);
				$subQuery = $db->get()->result();
				$row->perkaras = array_map(function ($p) {
					return $p->nomor_perkara;
				}, $subQuery);
			}
		}

		return $result;
	}
}
