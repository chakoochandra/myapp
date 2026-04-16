<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Folder Configuration
|--------------------------------------------------------------------------
|
| Central configuration for all folder paths used in the application.
| Organized by category for easier maintenance.
|
| Usage:
|   $this->config->item('folder_root_assets')
|   $this->config->item('folder_root_upload')
|   $this->config->item('folder_laporan')
|   etc.
|
*/

/*
|--------------------------------------------------------------------------
| Root Folders
|--------------------------------------------------------------------------
*/
$config['folder_root_assets'] = './assets/';
$config['folder_root_upload'] = './uploads/';

/*
|--------------------------------------------------------------------------
| Document Folders
|--------------------------------------------------------------------------
*/
$config['folder_laporan'] = 'laporan';
$config['folder_laporan_doc'] = 'laporan_doc';
$config['folder_sk'] = 'sk_documents';
$config['folder_sop'] = 'sop_documents';

/*
|--------------------------------------------------------------------------
| Letter Folders
|--------------------------------------------------------------------------
*/
$config['folder_surat_masuk'] = 'incoming_letters';
$config['folder_surat_keluar'] = 'outgoing_letters';
$config['folder_surat_cuti'] = 'surat_cuti';
$config['folder_surat_cuti_canceled'] = 'surat_cuti_canceled';

/*
|--------------------------------------------------------------------------
| Employee/Misc Folders
|--------------------------------------------------------------------------
*/
$config['folder_misc_pegawai'] = 'misc_pegawai';

/*
|--------------------------------------------------------------------------
| Monitoring Folders
|--------------------------------------------------------------------------
*/
$config['folder_relaas_ghaib'] = 'relaas_ghaib';
$config['folder_pbt_ghaib'] = 'pbt_ghaib';
