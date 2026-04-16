<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Report Type Configuration
|--------------------------------------------------------------------------
|
| Defines report type IDs for various official reports
| used in the reporting module.
|
| Usage:
|   $this->config->item('laporan_panggilan_ghaib')
|   $this->config->item('laporan_agenda_pimpinan')
|   $this->config->item('laporan_dipa_01')
|   etc.
|
*/

/*
|--------------------------------------------------------------------------
| Agenda & Meeting Reports
|--------------------------------------------------------------------------
*/
$config['laporan_panggilan_ghaib'] = 6;
$config['laporan_agenda_pimpinan'] = 28;
$config['laporan_agenda_kantor'] = 29;
$config['laporan_agenda_rapat_dinas'] = 30;
$config['laporan_agenda_zoom'] = 31;

/*
|--------------------------------------------------------------------------
| Legal & Discipline Reports
|--------------------------------------------------------------------------
*/
$config['laporan_hukuman_disiplin'] = 21;
$config['laporan_hasil_penelitian'] = 38;

/*
|--------------------------------------------------------------------------
| Financial Reports (DIPA)
|--------------------------------------------------------------------------
*/
$config['laporan_dipa_01'] = 41;
$config['laporan_dipa_04'] = 42;
$config['laporan_lra_dipa_01'] = 9;
$config['laporan_lra_dipa_04'] = 43;
$config['laporan_calk_dipa_01'] = 10;
$config['laporan_calk_dipa_04'] = 44;
$config['laporan_neraca_dipa_01'] = 11;
$config['laporan_neraca_dipa_04'] = 46;

/*
|--------------------------------------------------------------------------
| Asset Reports
|--------------------------------------------------------------------------
*/
$config['laporan_aset_inventaris_01'] = 47;
$config['laporan_aset_inventaris_04'] = 48;

/*
|--------------------------------------------------------------------------
| Revenue Reports (PNBP)
|--------------------------------------------------------------------------
*/
$config['laporan_realisasi_pnbp_01'] = 12;
$config['laporan_realisasi_pnbp_04'] = 49;

/*
|--------------------------------------------------------------------------
| Other Reports
|--------------------------------------------------------------------------
*/
$config['laporan_pengumuman_pengambilan_sisa_panjar'] = 32;
$config['laporan_pemberitahuan_pbt'] = 45;
