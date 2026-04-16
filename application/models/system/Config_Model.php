<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Config_Model extends CI_Model
{
	function get_all()
	{
		$result = array();
		
		// Check if db_sipp configuration exists by attempting to load it safely
		$db_sipp = null;
		$db_sipp_exists = false;
		
		// Try to load the database configuration to check if db_sipp exists
		// We'll use a try-catch approach to handle the error when db_sipp is not configured
		$CI =& get_instance();
		
		// Check if db_sipp configuration exists in the database config
		$db_config_file = APPPATH . 'config/database.php';
		if (file_exists($db_config_file)) {
			// Load the config file to check for db_sipp
			$config_content = file_get_contents($db_config_file);
			
			// Check if db_sipp configuration exists in the file
			if (strpos($config_content, "'db_sipp'") !== false || strpos($config_content, '"db_sipp"') !== false) {
				$db_sipp_exists = true;
			}
		}
		
		// Only attempt to load db_sipp if it exists in the config
		if ($db_sipp_exists) {
			// Attempt to load the db_sipp database
			$db_sipp = $this->load->database('db_sipp', TRUE);
			
			// Check if db_sipp is available and attempt to get data from it
			if ($db_sipp && $db_sipp instanceof CI_DB) {
				// Test the connection by attempting a simple query
				$db_sipp->from('sys_config');
				$query = @$db_sipp->get(); // Suppress errors with @
				
				// If query was successful, merge the data
				if ($query) {
					$sipp_configs = $query->result();
					$result = array_merge($result, $sipp_configs);
				} else {
					// If query failed, log the issue and continue without sipp configs
					log_message('debug', 'Config_Model: db_sipp not available or sys_config table does not exist');
				}
			}
		}
		
		// Always get data from the main database configs table
		$app_configs = $this->db->from($this->db->database . '.' . $this->config->item('tbl_configs'))->order_by('category ASC, key ASC, value ASC')->get()->result();
		$result = array_merge($result, $app_configs);
		
		return $result;
	}
}
