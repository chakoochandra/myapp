<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Position and Group Configuration
|--------------------------------------------------------------------------
|
| Defines job positions (jabatan), position groups, and user groups
| used throughout the application for access control and organization.
|
| Usage:
|   $this->config->item('jabatan_ketua')
|   $this->config->item('group_administrator')
|   etc.
|
*/

/*
|--------------------------------------------------------------------------
| Job Positions (Jabatan)
|--------------------------------------------------------------------------
|
| Individual position IDs for organizational structure
|
*/
$config['jabatan_ketua'] = 1;
$config['jabatan_wakil_ketua'] = 2;
$config['jabatan_hakim'] = 3;
$config['jabatan_panitera'] = 4;
$config['jabatan_sekretaris'] = 5;
$config['jabatan_panmud_gugatan'] = 6;
$config['jabatan_panmud_hukum'] = 7;
$config['jabatan_panmud_permohonan'] = 8;
$config['jabatan_kasub_kepegawaian'] = 9;
$config['jabatan_kasub_ptip'] = 10;
$config['jabatan_kasub_umum'] = 11;
$config['jabatan_pp'] = 12;
$config['jabatan_honorer'] = 17;
$config['jabatan_outsourcing'] = 18;

/*
|--------------------------------------------------------------------------
| Job Position Groups (Kelompok Jabatan)
|--------------------------------------------------------------------------
|
| Groups for categorizing positions
|
*/
$config['jabatan_group_hakim'] = 1;
$config['jabatan_group_panmud'] = 2;
$config['jabatan_group_kasubbag'] = 3;
$config['jabatan_group_jafung'] = 4;
$config['jabatan_group_pp'] = 5;
$config['jabatan_group_js'] = 6;
$config['jabatan_group_pelaksana'] = 7;
$config['jabatan_group_honorer'] = 8;

/*
|--------------------------------------------------------------------------
| User Groups
|--------------------------------------------------------------------------
|
| User group names for access control and permissions
|
*/
$config['group_administrator'] = 'Administrator';
$config['group_operator'] = 'Operator';
$config['group_tekre'] = 'Tekre';
$config['group_pp'] = 'PP';
$config['group_ptsp'] = 'PTSP';
$config['group_pegawai'] = 'Pegawai';
$config['group_posbakum'] = 'Posbakum';
$config['group_mediator'] = 'Mediator';
$config['group_pos'] = 'Pos';
$config['group_bank'] = 'Bank';
$config['group_disdukcapil'] = 'Disdukcapil';

/*
|--------------------------------------------------------------------------
| Special Groups (Kepegawaian)
|--------------------------------------------------------------------------
*/
$config['group_kepegawaian'] = 'Kepegawaian';
$config['group_plt_kasub_kepegawaian'] = 'Plt. Kepala Sub Bagian Kepegawaian, Organisasi, Dan Tata Laksana';
$config['group_plt_panitera'] = 'Plt. Panitera';
$config['group_plt_sekretaris'] = 'Plt. Sekretaris';
$config['group_plt_wakil_ketua'] = 'Plt. Wakil Ketua';
$config['group_plt_ketua'] = 'Plt. Ketua';

/*
|--------------------------------------------------------------------------
| Special Group IDs
|--------------------------------------------------------------------------
*/
$config['group_id_plt_kasub_kepegawaian'] = 10;
$config['group_id_plt_panitera'] = 11;
$config['group_id_plt_sekretaris'] = 12;
$config['group_id_plt_wakil_ketua'] = 13;
$config['group_id_plt_ketua'] = 14;

/*
|--------------------------------------------------------------------------
| Key Group IDs
|--------------------------------------------------------------------------
*/
$config['id_group_administrator'] = 1;
$config['id_group_kepegawaian'] = 9;
$config['id_group_pegawai'] = 99;
