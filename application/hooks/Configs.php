<?php

class Configs
{
	function get_configs()
	{
		$CI = &get_instance();
		$CI->load->model('system/Config_Model', 'app_config');
		foreach ($CI->app_config->get_all() as $row) {
			$key = isset($row->key) ? $row->key : $row->name;
			$value = $row->value;

			// Load into CodeIgniter config array (preferred method)
			$CI->config->set_item($key, $value);

			// Also define as constant for backward compatibility
			defined($key) or define($key, $value);
		}

		// Only perform the kode_satker check if both constants are defined
		// if (defined('kode_satker') && hash('sha256', kode_satker) !== the) exit;
	}
}
