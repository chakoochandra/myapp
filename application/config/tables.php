<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Database Table Configuration
|--------------------------------------------------------------------------
|
| Central configuration for all database table names used in the application.
| Organized by category for easier maintenance.
|
| Usage:
|   $this->config->item('tbl_ref_golongan')
|   $this->config->item('tbl_users')
|   $this->config->item('tbl_laporan')
|   etc.
|
*/

/*
|--------------------------------------------------------------------------
| Reference Tables
|--------------------------------------------------------------------------
*/
$config['tbl_ref_golongan'] = 'tref_rank_grade';
$config['tbl_ref_jabatan'] = 'tref_official_position';
$config['tbl_ref_jenis_laporan'] = 'tref_report_type';
$config['tbl_ref_jenis_libur'] = 'tref_holiday_type';
$config['tbl_ref_menu'] = 'menus';
$config['tbl_ref_tingkat_pendidikan'] = 'tref_education_level';
$config['tbl_ref_wilayah'] = 'tref_wilayah_2020';

/*
|--------------------------------------------------------------------------
| Core System Tables
|--------------------------------------------------------------------------
*/
$config['tbl_configs'] = 'tmst_configs';
$config['tbl_groups'] = 'groups';
$config['tbl_users'] = 'users';
$config['tbl_users_configs'] = 'users_configs';
$config['tbl_users_groups'] = 'users_groups';

/*
|--------------------------------------------------------------------------
| Queue Tables
|--------------------------------------------------------------------------
*/
$config['tbl_queue'] = 'trans_queue';
$config['tbl_checkin'] = 'trans_queue_check_in';
$config['tbl_increment'] = 'trans_queue_increment';

/*
|--------------------------------------------------------------------------
| Employee & Organization Tables
|--------------------------------------------------------------------------
*/
$config['tbl_alamat'] = 'tmst_address';
$config['tbl_jobdesc'] = 'tmst_jobdesc';
$config['tbl_misc_pegawai'] = 'trans_employee_data';
$config['tbl_struktur_organisasi'] = 'tmst_organization_structure';
$config['tbl_holiday'] = 'tmst_holiday';

/*
|--------------------------------------------------------------------------
| Document Tables
|--------------------------------------------------------------------------
*/
$config['tbl_laporan'] = 'trans_report';
$config['tbl_sk_documents'] = 'tmst_sk_documents';
$config['tbl_sk_views'] = 'trans_sk_views';
$config['tbl_sk_notifications'] = 'sk_notifications';
$config['tbl_sop'] = 'tmst_sop_documents';

/*
|--------------------------------------------------------------------------
| Letter Management Tables
|--------------------------------------------------------------------------
*/
$config['tbl_surat_keluar'] = 'tmst_outgoing_letters';
$config['tbl_surat_masuk'] = 'tmst_incoming_letters';

/*
|--------------------------------------------------------------------------
| Leave Management Tables (Cuti)
|--------------------------------------------------------------------------
*/
$config['tbl_cuti'] = 'trans_employee_leaves';
$config['tbl_sisa_cuti'] = 'tmst_leave_balances';
$config['tbl_ref_jenis_cuti'] = 'tref_leave_types';
$config['tbl_ref_pertimbangan_cuti'] = 'tref_leave_consideration';
$config['tbl_ref_status_cuti'] = 'tref_leave_status';

/*
|--------------------------------------------------------------------------
| Communication Tables
|--------------------------------------------------------------------------
*/
$config['tbl_audio'] = 'tmst_audio';
$config['tbl_audio_schedules'] = 'tmst_audio_schedules';
$config['tbl_panggilan'] = 'trans_text_to_audio';
$config['tbl_whatsapp'] = 'trans_whatsapp_message';

/*
|--------------------------------------------------------------------------
| Guest & Person Tables
|--------------------------------------------------------------------------
*/
$config['tbl_guests'] = 'tmst_guest';
$config['tbl_persons'] = 'tmst_person';

/*
|--------------------------------------------------------------------------
| Other Tables
|--------------------------------------------------------------------------
*/
$config['tbl_log'] = 'trans_log';
$config['tbl_gallery'] = 'tmst_gallery';
