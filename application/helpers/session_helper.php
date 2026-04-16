<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_or_set_jabatan_struktur')) {
	function get_or_set_jabatan_struktur($fromSession = true)
	{
		$CI = get_instance();
		if ($fromSession && $CI->session->userdata("jabatan_need_atasan") !== null) {
			return $CI->session->userdata('jabatan_need_atasan');
		}

		$CI->load->model('pegawai/Jabatan_Model', 'jabatan');

		$ids = [];
		foreach ($CI->jabatan->find(['where' => ['(struktur_organisasi_id IS NOT NULL)']]) as $j) {
			$ids[$j->id] = $j->struktur_organisasi_id;
		}

		$CI->session->set_userdata("jabatan_need_atasan", $ids);

		return $ids;
	}
}

if (!function_exists('get_or_set_jabatan_honorer')) {
	function get_or_set_jabatan_honorer($fromSession = true)
	{
		$CI = get_instance();
		// if ($fromSession && $CI->session->userdata("jabatan_honorer") !== null) {
		//     return $CI->session->userdata('jabatan_honorer');
		// }

		$CI->load->model('pegawai/Jabatan_Model', 'jabatan');

		$ids = [];
		foreach ($CI->jabatan->find(['where' => ['(is_honorer = 1)']]) as $j) {
			$ids[] = $j->id;
		}

		$CI->session->set_userdata("jabatan_honorer", $ids);

		return $ids;
	}
}

if (!function_exists('get_or_set_broadcast')) {
	function get_or_set_broadcast($fromSession = true)
	{
		$CI = get_instance();
		if ($fromSession && $CI->session->userdata("audio_broadcast") !== null) {
			return $CI->session->userdata('audio_broadcast');
		}

		$CI->load->model('audio/Schedule_Model', 'schedule');

		$day = date('w');
		$audios = [];
		$CI->db->select($CI->config->item('tbl_audio_schedules') . '.*, ' . $CI->config->item('tbl_audio') . '.file_audio, ' . $CI->config->item('tbl_audio') . '.volume');
		$CI->db->from($CI->config->item('tbl_audio_schedules'));
		$CI->db->join($CI->config->item('tbl_audio'), $CI->config->item('tbl_audio') . '.id = ' . $CI->config->item('tbl_audio_schedules') . '.audio_id', 'left');
		$CI->db->where([$CI->config->item('tbl_audio_schedules') . '.status' => 1, $CI->config->item('tbl_audio') . '.status' => 1]);
		$query = $CI->db->get();
		$results = $query->result();

		foreach ($results as $i => $row) {
			if (in_array($day, explode(',', $row->days))) {
				$audios[$row->time][] = [base_url() . get_instance()->config->item('folder_root_upload') . 'file_audio/' . $row->file_audio, $row->volume];
			}
		}

		$CI->session->set_userdata("audio_broadcast", $audios);

		return $audios;
	}
}
