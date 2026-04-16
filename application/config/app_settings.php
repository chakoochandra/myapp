<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Application Settings Configuration
|--------------------------------------------------------------------------
|
| General application settings, feature flags, and UI configuration.
|
| Usage:
|   $this->config->item('source_version')
|   $this->config->item('node_default_color')
|   $this->config->item('enable_test')
|
*/

/*
|--------------------------------------------------------------------------
| Application Version
|--------------------------------------------------------------------------
*/
$config['source_version'] = '1';

/*
|--------------------------------------------------------------------------
| Theme Settings
|--------------------------------------------------------------------------
*/
$config['node_default_color'] = "green";

/*
|--------------------------------------------------------------------------
| Feature Flags
|--------------------------------------------------------------------------
|
| Enable/disable various application features
|
*/
$config['enable_test'] = ENVIRONMENT == 'development' ? TRUE : FALSE;
$config['enable_refresh'] = ENVIRONMENT == 'development' ? FALSE : TRUE;
$config['enable_antrian_mediasi'] = TRUE;
$config['allow_empty_ttd'] = TRUE;
$config['enable_cuti'] = false;
$config['kode_perkara_satker'] = 'PA.Sda';
