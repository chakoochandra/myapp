<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Status Codes and Types Configuration
|--------------------------------------------------------------------------
|
| Defines status codes, types, and other classification constants
| used throughout the application.
|
| Usage:
|   $this->config->item('id_wilayah_lain')
|   $this->config->item('antrian_status_init')
|   $this->config->item('cuti_tahunan')
|   $this->config->item('status_verify_kepegawaian')
|   etc.
|
*/

/*
|--------------------------------------------------------------------------
| Region & Case Type
|--------------------------------------------------------------------------
*/
$config['id_wilayah_lain'] = '00.00';
$config['id_jenis_contentius'] = 1;
$config['id_jenis_voluntair'] = 2;

/*
|--------------------------------------------------------------------------
| Queue (Antrian) Status
|--------------------------------------------------------------------------
*/
$config['antrian_status_init'] = 0;
$config['antrian_status_on_progress'] = 1;
$config['antrian_status_finished'] = 2;
$config['antrian_status_skors'] = 3;
$config['antrian_status_cancel'] = 9;

/*
|--------------------------------------------------------------------------
| Leave (Cuti) Types
|--------------------------------------------------------------------------
*/
$config['cuti_tahunan'] = 1;
$config['cuti_sakit'] = 2;
$config['cuti_cap'] = 3;
$config['cuti_besar'] = 4;
$config['cuti_melahirkan'] = 5;
$config['cuti_cltn'] = 6;

/*
|--------------------------------------------------------------------------
| Leave (Cuti) Status
|--------------------------------------------------------------------------
*/
$config['cuti_disetujui'] = 1;
$config['cuti_perubahan'] = 2;
$config['cuti_ditangguhkan'] = 3;
$config['cuti_ditolak'] = 4;

/*
|--------------------------------------------------------------------------
| Holiday Types
|--------------------------------------------------------------------------
*/
$config['libur_nasional_tanggal_tetap'] = 0;
$config['libur_cuti_bersama_mengurangi_cuti_tahunan'] = 3;

/*
|--------------------------------------------------------------------------
| Verification & Workflow Status
|--------------------------------------------------------------------------
*/
$config['status_verify_kepegawaian'] = 1;
$config['status_verify_atasan'] = 2;
$config['status_verify_pejabat'] = 3;
$config['status_validasi_kepegawaian'] = 4;
$config['status_published'] = 5;
$config['status_canceled'] = 6;
$config['status_manual'] = 98;
$config['status_rejected'] = 99;

/*
|--------------------------------------------------------------------------
| Note/Call Types
|--------------------------------------------------------------------------
*/
$config['note_call_sidang'] = 'sidang';
$config['note_call_ac'] = 'petugas_ac';
$config['note_call_mediasi'] = 'petugas_mediasi';

/*
|--------------------------------------------------------------------------
| Notification Types
|--------------------------------------------------------------------------
*/
$config['notif_jadwal_sidang'] = 'Notifikasi Jadwal Sidang';
$config['notif_antrian_sidang'] = 'Notifikasi Antrian Sidang';
$config['notif_sidang_keliling'] = 'Notifikasi Sidang Keliling';
$config['notif_elitigasi'] = 'Notifikasi e-Litigasi';
$config['notif_calendar'] = 'Notifikasi Court Calendar';
$config['notif_journal'] = 'Notifikasi PSP';
$config['notif_akta_cerai'] = 'Notifikasi Akta Cerai';
