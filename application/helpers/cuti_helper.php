<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_jenis_cuti_text')) {
    function get_jenis_cuti_text($id)
    {
        $CI = get_instance();
        switch ($id) {
            case $CI->config->item('cuti_tahunan'):
                return 'Cuti Tahunan';
            case $CI->config->item('cuti_sakit'):
                return 'Cuti Sakit';
            case $CI->config->item('cuti_cap'):
                return 'Cuti Karena Alasan Penting';
            case $CI->config->item('cuti_besar'):
                return 'Cuti Besar';
            case $CI->config->item('cuti_melahirkan'):
                return 'Cuti Melahirkan';
            case $CI->config->item('cuti_cltn'):
                return 'Cuti di Luar Tanggungan Negara';
        }
    }
}

if (!function_exists('has_jatah_cuti_tahunan')) {
    function has_jatah_cuti_tahunan($id_jenis_cuti)
    {
        return jatah_cuti_tahunan($id_jenis_cuti) !== false;
    }
}

if (!function_exists('jatah_cuti_tahunan')) {
    function jatah_cuti_tahunan($id_jenis_cuti)
    {
        $CI = get_instance();
        $jatah =  [
            $CI->config->item('cuti_tahunan') => $CI->config->item('cuti_reguler_pertahun'),
            $CI->config->item('cuti_sakit') => $CI->config->item('cuti_sakit_pertahun'),
            $CI->config->item('cuti_cap') => $CI->config->item('cuti_cap_pertahun'),
        ];
        return isset($jatah[$id_jenis_cuti]) ? $jatah[$id_jenis_cuti] : false;
    }
}

if (!function_exists('count_working_days')) {
    function count_working_days($startDate, $endDate)
    {
        $countCuti = 0;
        if ($startDate && $endDate) {
            $CI = get_instance();

            $CI->load->model('system/Holiday_Model', 'holiday');

            $holidays = $CI->holiday->get();
            foreach (new DatePeriod(new DateTime($startDate), DateInterval::createFromDateString('1 day'), (new DateTime($endDate))->modify('+1 day')) as $date) {
                if (!is_holiday($date, $holidays)) {
                    $countCuti++;
                }
            }
        }
        return $countCuti;
    }
}

if (!function_exists('cuti_color')) {
    function cuti_color($jenis_cuti_id)
    {
        $CI = get_instance();
        $class = [
            $CI->config->item('cuti_tahunan') => '#28a745',
            $CI->config->item('cuti_sakit') => '#ffc107',
            $CI->config->item('cuti_cap') => '#dc3545',
            $CI->config->item('cuti_melahirkan') => '#6f42c1',
            $CI->config->item('cuti_besar') => 'forestgreen',
            $CI->config->item('cuti_cltn') => '#6c757d',
        ];
        return $class[$jenis_cuti_id];
    }
}

if (!function_exists('cuti_bg_class')) {
    function cuti_bg_class($jenis_cuti_id)
    {
        $CI = get_instance();
        $class = [
            $CI->config->item('cuti_tahunan') => '#3c8dbc',
            $CI->config->item('cuti_sakit') => '#00a65a',
            $CI->config->item('cuti_cap') => '#00c0ef',
            $CI->config->item('cuti_melahirkan') => '#f39c12',
            $CI->config->item('cuti_besar') => '#f56954',
            $CI->config->item('cuti_cltn') => '#f39c12',
        ];
        return $class[$jenis_cuti_id];
    }
}

if (!function_exists('laporan_bg_class')) {
    function laporan_bg_class($jenis_laporan_id)
    {
        $CI = get_instance();
        $class = [
            $CI->config->item('laporan_agenda_pimpinan') => '#00a65a',
            $CI->config->item('laporan_agenda_kantor') => '#3c8dbc',
            $CI->config->item('laporan_agenda_rapat_dinas') => '#00c0ef',
            $CI->config->item('laporan_agenda_zoom') => '#f39c12',
        ];
        return $class[$jenis_laporan_id];
    }
}

if (!function_exists('jabatan_atasan_cuti')) {
    function jabatan_atasan_cuti()
    {
        $CI = get_instance();
        return [$CI->config->item('jabatan_sekretaris'), $CI->config->item('jabatan_panitera'), $CI->config->item('jabatan_wakil_ketua'), $CI->config->item('jabatan_ketua')];
    }
}

if (!function_exists('status_cuti_on_process')) {
    function status_cuti_on_process()
    {
        $CI = get_instance();
        return [
            $CI->config->item('status_verify_kepegawaian'),
            $CI->config->item('status_verify_atasan'),
            $CI->config->item('status_verify_pejabat'),
            $CI->config->item('status_validasi_kepegawaian'),
        ];
    }
}

if (!function_exists('get_cuti_action')) {
    function get_cuti_action($cuti, $canEdit = true)
    {
        $CI = &get_instance();

        $pegawaiInvalid = is_pegawai_invalid($cuti);
        $needDataDukung = !($urlDataDukung = file_url('data_dukung_cuti', $cuti->data_dukung_cuti)) && $cuti->jenis_cuti_id != $CI->config->item('cuti_tahunan');
        $pengajuanCutiKetua = $cuti->status == $CI->config->item('status_validasi_kepegawaian') && $cuti->jabatan_id == $CI->config->item('jabatan_ketua');

        $html = '';
        if ($pegawaiInvalid && (is_kepegawaian() || $CI->ion_auth->is_admin())) {
            $html .= anchor(base_url("{$CI->from}/update_profil/{$cuti->user_id}"), '<span class="fa fa-pencil" aria-hidden="true"></span> Lengkapi Data', [
                'class' => 'btn btn-xs btn-outline-danger btn-modal',
                'title' => 'Lengkapi Data',
            ]);
        } else if ($needDataDukung && (is_kepegawaian() || $CI->ion_auth->is_admin())) {
            $html .= $canEdit ? anchor(base_url("{$CI->from}/update_cuti/{$cuti->id}"), '<span class="fa fa-file-pdf-o" aria-hidden="true"></span> Unggah Data Dukung', [
                'class' => 'btn btn-xs btn-outline-danger btn-modal',
                'title' => 'Unggah Data Dukung',
            ]) : '';
        } else {
            if (in_array($cuti->status, [$CI->config->item('status_published'), $CI->config->item('status_canceled'), $CI->config->item('status_manual')])) {
                $html .= anchor(base_url("{$CI->from}/view_cuti/{$cuti->id}"), '<i class="fa fa-eye"></i>', [
                    'title' => 'Detail Cuti',
                    'class' => 'btn btn-xs btn-xs-fix-width btn-outline-success btn-modal',
                ]);
            } else {
                $html .= anchor_popup(base_url("cuti/preview/{$cuti->id}"), '<i class="fa fa-eye"></i>', [
                    'width'   => '800',
                    'height'  => '600',
                    'scrollbars' => 'yes',
                    'status'  => 'yes',
                    'resizable'  => 'yes',
                    'screenx' => '0',
                    'screeny' => '0',
                    'title' => 'Preview Surat Cuti',
                    'class' => 'btn btn-xs btn-xs-fix-width btn-outline-success',
                ]);
            }

            if ($cuti->jenis_cuti_id != $CI->config->item('cuti_tahunan')) {
                $html .= $urlDataDukung ? anchor_popup($urlDataDukung, '<i class="fa fa-file-pdf-o"></i>', [
                    'width'   => '800',
                    'height'  => '600',
                    'scrollbars' => 'yes',
                    'status'  => 'yes',
                    'resizable'  => 'yes',
                    'screenx' => '0',
                    'screeny' => '0',
                    'title' => 'Data Dukung',
                    'class' => 'btn btn-xs btn-xs-fix-width btn-outline-success',
                ]) : '<i class="fa fa-file-pdf-o text-danger"></i>';
            }

            $html .= $canEdit && $cuti->status == $CI->config->item('status_manual') ? anchor(base_url("{$CI->from}/update_manual/{$cuti->id}"), '<i class="fa fa-edit"></i>', [
                'title' => 'Perbarui data cuti',
                'class' => 'btn btn-xs btn-xs-fix-width btn-outline-warning btn-modal',
            ]) : '';

            if ($canEdit) {
                if (
                    in_array($cuti->status, [$CI->config->item('status_verify_kepegawaian'), $CI->config->item('status_rejected')]) ||
                    (in_array($cuti->status, status_cuti_on_process()) &&
                        ($pengajuanCutiKetua ||
                            ($cuti->pertimbangan_atasan_id && $cuti->pertimbangan_atasan_id != $CI->config->item('cuti_disetujui')) ||
                            ($cuti->pertimbangan_pejabat_id && $cuti->pertimbangan_pejabat_id != $CI->config->item('cuti_disetujui'))))
                ) {
                    $html .= anchor(base_url("{$CI->from}/update_cuti/{$cuti->id}"), '<i class="fa fa-edit"></i>', [
                        'title' => 'Perbarui Pengajuan',
                        'class' => 'btn btn-xs btn-xs-fix-width btn-outline-warning btn-modal',
                    ]);
                }
            }

            // $html .= $canEdit && (($cuti->status == $CI->config->item('status_verify_pejabat') && $cuti->pertimbangan_atasan_id != $CI->config->item('cuti_disetujui')) || in_array($cuti->status, [$CI->config->item('status_verify_kepegawaian'), $CI->config->item('status_rejected')]) || $pengajuanCutiKetua) ? anchor(base_url("{$CI->from}/update_cuti/{$cuti->id}"), '<i class="fa fa-edit"></i>', [
            //     'title' => 'Perbarui Pengajuan',
            //     'class' => 'btn btn-xs btn-xs-fix-width btn-outline-warning btn-modal',
            // ]) : '';

            $html .= $canEdit && is_kasub_kepegawaian() && (in_array($cuti->status, [$CI->config->item('status_verify_kepegawaian'), $CI->config->item('status_rejected')]) || $pengajuanCutiKetua) ? anchor_confirm(base_url("{$CI->from}/delete_cuti/{$cuti->id}"), '<i class="fa fa-trash"></i>', [
                'title' => 'Hapus Pengajuan',
                'data-confirm-message' => "Anda yakin akan menghapus pengajuan {$cuti->jenis_cuti} {$cuti->nama_pegawai}?",
                'class' => 'btn btn-xs btn-xs-fix-width btn-outline-danger',
            ]) : null;
        }

        return $html;
    }
}

if (!function_exists('get_status_class')) {
    function get_status_class($id_status)
    {
        $CI = get_instance();
        $class = [
            $CI->config->item('status_verify_kepegawaian') => 'default',
            $CI->config->item('status_verify_atasan') => 'primary',
            $CI->config->item('status_verify_pejabat') => 'primary',
            $CI->config->item('status_validasi_kepegawaian') => 'info',
            $CI->config->item('status_published') => 'success',
            $CI->config->item('status_manual') => 'success',
            // $CI->config->item('status_manual') => 'manual',
        ];

        return isset($class[$id_status]) ? $class[$id_status] : 'danger';
    }
}

if (!function_exists('get_status_badge')) {
    function get_status_badge($cuti)
    {
        $CI = get_instance();
        $html = '<div><span class="badge badge-' . get_status_class($cuti->status) . '">' .  $cuti->status_cuti . '</span></div>';

        if ($cuti->pertimbangan_atasan_id && $cuti->pertimbangan_atasan_id != $CI->config->item('cuti_disetujui')) {
            $html .= '<div><span class="badge badge-warning">' .  anchor(base_url("cuti/view_pertimbangan/{$cuti->id}"), $cuti->pertimbangan_atasan . ' ' . $cuti->nama_jabatan_atasan, [
                'class' => 'btn-modal',
                'title' => $cuti->pertimbangan_atasan_text ?: 'Tidak ada keterangan',
            ]) . '</span></div>';
        }

        if ($cuti->pertimbangan_pejabat_id && $cuti->pertimbangan_pejabat_id != $CI->config->item('cuti_disetujui')) {
            $html .= '<div><span class="badge badge-warning">' .  anchor(base_url("cuti/view_pertimbangan/{$cuti->id}"), $cuti->pertimbangan_pejabat . ' ' . $cuti->nama_jabatan_pejabat, [
                'class' => 'btn-modal',
                'title' => $cuti->pertimbangan_atasan_text ?: 'Tidak ada keterangan',
            ]) . '</span></div>';
        }

        return $html;
    }
}

if (!function_exists('is_pegawai_invalid')) {
    function is_pegawai_invalid($pegawai)
    {
        return get_instance()->config->item('allow_empty_ttd') ? $pegawai->invalid == 1 : ($pegawai->invalid == 1 || !file_url('ttd', $pegawai->ttd));
    }
}


if (!function_exists('formatAddress')) {
    function formatAddress($address)
    {
        $html = $address->alamat_lengkap . ', ';
        if ($address->rt || $address->rw) {
            $html .= $address->rt ? 'RT ' . $address->rt : '';
            $html .= ($address->rw ? ' / RW ' . $address->rw : '') . ', ';
        }

        $html .= $address->kelurahan ? $address->kelurahan . ', ' : '';
        $html .= $address->kecamatan . ', ';
        $html .= $address->kabupaten;
        return html_escape($html);
    }
}

if (!function_exists('getMiscType')) {
    function getMiscType($type = null)
    {
        $all = ['LHKPN', 'SPT', 'Penghargaan', 'Pendidikan', 'Jabatan'];
        return !is_null($type) ? $all[$type] : $all;
    }
}
