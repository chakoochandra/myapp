<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('pattern_nama_pihak')) {
    function pattern_nama_pihak($namaPihak, $namaPengacaraP = '', $namaPengacaraT = '')
    {
        $pengacaraPDiv = ($namaPengacaraP ? "<div class='d-flex justify-content-center item-aligns-center mb-1 text-xs'><span class='badge badge-primary'>Pengacara: $namaPengacaraP</span></div>" : '') . '<div class="d-flex justify-content-center item-aligns-center mb-1 text-xs">%s</div>';
        $pengacaraTDiv = ($namaPengacaraT ? "<div class='d-flex justify-content-center item-aligns-center mb-1 text-xs'><span class='badge badge-primary'>Pengacara: $namaPengacaraT</span></div>" : '') . '<div class="d-flex justify-content-center item-aligns-center mb-1 text-xs">%s</div>';

        // Mencari posisi kata kunci "Termohon" atau "Tergugat" dalam string dan memecah string menjadi dua bagian berdasarkan kata kunci yang ditemukan
        if (($posT = strpos($namaPihak, "Termohon:")) !== false) {
            return substr($namaPihak, 0, $posT) . $pengacaraPDiv . (($str = substr($namaPihak, $posT)) ? $str : '') . $pengacaraTDiv;
        }

        if (($posT = strpos($namaPihak, "Tergugat:")) !== false) {
            return substr($namaPihak, 0, $posT) . $pengacaraPDiv . (($str = substr($namaPihak, $posT)) ? $str : '') . $pengacaraTDiv;
        }

        return $namaPihak . $pengacaraPDiv;
    }
}

if (!function_exists('get_nama_pihak_only')) {
    function get_nama_pihak_only($nama)
    {
        $namaList = explode("<br />", $nama);
        $namaArray = explode(" ", strtolower(trim(preg_replace('/[\d.]+/', '', $namaList[0]))));

        // Mengambil nama yang tepat berdasarkan indeks kata "bin" atau "binti"
        if (($indexBin = array_search("bin", $namaArray)) !== false) {
            $namaAkhir = implode(" ", array_slice($namaArray, 0, $indexBin));
        } elseif (($indexBinti = array_search("binti", $namaArray)) !== false) {
            $namaAkhir = implode(" ", array_slice($namaArray, 0, $indexBinti));
        } else {
            $namaAkhir = implode(" ", $namaArray); // Jika tidak ditemukan "bin" atau "binti"
        }

        return trim($namaAkhir) . (count($namaList) > 1 ? ' dan seterusnya' : '');
    }
}

if (!function_exists('get_pihak')) {
    function get_pihak($type = null)
    {
        $pihaks =  [
            'pihak' => 'PIHAK',
            'saksip' => 'SAKSI [P]',
            'saksit' => 'SAKSI [T]',
            'kuasap' => 'KUASA [P]',
            'kuasat' => 'KUASA [T]',
        ];
        return $type ? $pihaks[$type] : $pihaks;
    }
}

if (!function_exists('get_class_by_status')) {
    function get_class_by_status($type = null)
    {
        $CI = get_instance();
        $class =  [
            $CI->config->item('antrian_status_on_progress') => 'class="ongoing"',
            $CI->config->item('antrian_status_finished') => 'class="finished"',
            $CI->config->item('antrian_status_cancel') => 'class="finished"',
            $CI->config->item('antrian_status_skors') => 'class="skors"',
        ];
        return isset($class[$type]) ? $class[$type] : '';
    }
}

if (!function_exists('get_text_status')) {
    function get_text_status($type = null)
    {
        $CI = get_instance();
        $class =  [
            $CI->config->item('antrian_status_init') => 'Mengantri',
            $CI->config->item('antrian_status_on_progress') => 'Sedang Proses',
            $CI->config->item('antrian_status_finished') => 'Selesai',
            $CI->config->item('antrian_status_skors') => 'Skors',
            $CI->config->item('antrian_status_cancel') => 'Batal',
        ];
        return isset($class[$type]) ? $class[$type] : '';
    }
}

if (!function_exists('can_access_sidang')) {
    function can_access_sidang()
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        return $CI->ion_auth_model->in_group([$CI->config->item('group_pp'), $CI->config->item('group_operator'), $CI->config->item('group_administrator')], $CI->user->id);
    }
}

if (!function_exists('can_access_antrian')) {
    function can_access_antrian()
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        return $CI->ion_auth_model->in_group([$CI->config->item('group_ptsp'), $CI->config->item('group_pp'), $CI->config->item('group_operator'), $CI->config->item('group_administrator')], $CI->user->id);
    }
}

if (!function_exists('can_access_posbakum')) {
    function can_access_posbakum()
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        return can_access_antrian() || $CI->ion_auth_model->in_group([$CI->config->item('group_posbakum')], $CI->user->id);
    }
}

if (!function_exists('can_access_pos')) {
    function can_access_pos()
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        return can_access_antrian() || $CI->ion_auth_model->in_group([$CI->config->item('group_pos')], $CI->user->id);
    }
}
if (!function_exists('can_access_bank')) {
    function can_access_bank()
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        return can_access_antrian() || $CI->ion_auth_model->in_group([$CI->config->item('group_bank')], $CI->user->id);
    }
}
if (!function_exists('can_access_disdukcapil')) {
    function can_access_disdukcapil()
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        return can_access_antrian() || $CI->ion_auth_model->in_group([$CI->config->item('group_disdukcapil')], $CI->user->id);
    }
}

if (!function_exists('can_access_mediasi')) {
    function can_access_mediasi()
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        return can_access_antrian() || $CI->ion_auth_model->in_group([$CI->config->item('group_mediator')], $CI->user->id);
    }
}

if (!function_exists('can_delete_file')) {
    function can_delete_file()
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        return $CI->ion_auth_model->in_group([$CI->config->item('group_pp'), $CI->config->item('group_operator'), $CI->config->item('group_administrator')], $CI->user->id);
    }
}

if (!function_exists('is_external_staff')) {
    function is_external_staff()
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        return $CI->ion_auth_model->in_group([$CI->config->item('group_posbakum'), $CI->config->item('group_pos')], $CI->user->id);
    }
}

if (!function_exists('is_priority')) {
    function is_priority($string)
    {
        $CI = get_instance();
        
        // Use config items (preferred CodeIgniter way)
        $ANTRIAN_TYPE_PRIORITY = $CI->config->item('ANTRIAN_TYPE_PRIORITY');
        $ANTRIAN_TYPE_ONLINE = $CI->config->item('ANTRIAN_TYPE_ONLINE');
        
        foreach ([$ANTRIAN_TYPE_PRIORITY, $ANTRIAN_TYPE_ONLINE] as $type) {
            if (stripos($string, $type) !== false) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('get_queue_config')) {
    function get_queue_config($type = null)
    {
        $CI = get_instance();
        
        // Use config items (preferred CodeIgniter way)
        // This works because Configs hook loads all tmst_configs into config array
        $ANTRIAN_TYPE_PENGADUAN = $CI->config->item('ANTRIAN_TYPE_PENGADUAN');
        $ANTRIAN_TYPE_MEJA1 = $CI->config->item('ANTRIAN_TYPE_MEJA1');
        $ANTRIAN_TYPE_KASIR = $CI->config->item('ANTRIAN_TYPE_KASIR');
        $ANTRIAN_TYPE_POSBAKUM = $CI->config->item('ANTRIAN_TYPE_POSBAKUM');
        $ANTRIAN_TYPE_ECOURT = $CI->config->item('ANTRIAN_TYPE_ECOURT');
        $ANTRIAN_TYPE_AC = $CI->config->item('ANTRIAN_TYPE_AC');
        $ANTRIAN_TYPE_POS = $CI->config->item('ANTRIAN_TYPE_POS');
        $ANTRIAN_TYPE_MEDIASI = $CI->config->item('ANTRIAN_TYPE_MEDIASI');
        $ANTRIAN_TYPE_BANK = $CI->config->item('ANTRIAN_TYPE_BANK');
        $ANTRIAN_TYPE_DISDUKCAPIL = $CI->config->item('ANTRIAN_TYPE_DISDUKCAPIL');
        $ANTRIAN_TYPE_SIDANGR1 = $CI->config->item('ANTRIAN_TYPE_SIDANGR1');
        $ANTRIAN_TYPE_SIDANGR2 = $CI->config->item('ANTRIAN_TYPE_SIDANGR2');
        $ANTRIAN_TYPE_SIDANGR3 = $CI->config->item('ANTRIAN_TYPE_SIDANGR3');
        $ANTRIAN_TYPE_ELITIGASI = $CI->config->item('ANTRIAN_TYPE_ELITIGASI');
        $ANTRIAN_TYPE_SIDANG_KELILING1 = $CI->config->item('ANTRIAN_TYPE_SIDANG_KELILING1');
        $ANTRIAN_TYPE_SIDANG_KELILING2 = $CI->config->item('ANTRIAN_TYPE_SIDANG_KELILING2');
        $ANTRIAN_TYPE_IKRAR1 = $CI->config->item('ANTRIAN_TYPE_IKRAR1');
        $ANTRIAN_TYPE_IKRAR2 = $CI->config->item('ANTRIAN_TYPE_IKRAR2');
        $ANTRIAN_TYPE_IKRAR3 = $CI->config->item('ANTRIAN_TYPE_IKRAR3');
        $ANTRIAN_TYPE_IKRAR_KELILING1 = $CI->config->item('ANTRIAN_TYPE_IKRAR_KELILING1');
        $ANTRIAN_TYPE_IKRAR_KELILING2 = $CI->config->item('ANTRIAN_TYPE_IKRAR_KELILING2');
        $ANTRIAN_TYPE_PRIORITY = $CI->config->item('ANTRIAN_TYPE_PRIORITY');
        $ANTRIAN_TYPE_ONLINE = $CI->config->item('ANTRIAN_TYPE_ONLINE');
        
        // NOTE_ANTRIAN constants from config
        $NOTE_ANTRIAN_FROM_PENGADUAN = $CI->config->item('NOTE_ANTRIAN_FROM_PENGADUAN');
        $NOTE_ANTRIAN_PANJAR = $CI->config->item('NOTE_ANTRIAN_PANJAR');
        $NOTE_ANTRIAN_FROM_KASIR = $CI->config->item('NOTE_ANTRIAN_FROM_KASIR');
        $NOTE_ANTRIAN_FROM_POSBAKUM = $CI->config->item('NOTE_ANTRIAN_FROM_POSBAKUM');
        $NOTE_ANTRIAN_FROM_ECOURT = $CI->config->item('NOTE_ANTRIAN_FROM_ECOURT');
        $NOTE_ANTRIAN_FROM_AC = $CI->config->item('NOTE_ANTRIAN_FROM_AC');
        $NOTE_ANTRIAN_FROM_POS = $CI->config->item('NOTE_ANTRIAN_FROM_POS');
        $NOTE_ANTRIAN_FROM_BANK = $CI->config->item('NOTE_ANTRIAN_FROM_BANK');
        $NOTE_ANTRIAN_FROM_DISDUKCAPIL = $CI->config->item('NOTE_ANTRIAN_FROM_DISDUKCAPIL');

        $can_access_antrian = can_access_antrian();
        $can_access_posbakum = can_access_posbakum();
        $can_access_pos = can_access_pos();
        $can_access_bank = can_access_bank();
        $can_access_disdukcapil = can_access_disdukcapil();
        $can_access_mediasi = can_access_mediasi();

        $config =  [
            $ANTRIAN_TYPE_PENGADUAN => [
                'prefix' => 'A',
                'title_list' => 'Antrian Pengaduan',
                'title_loket' => 'Loket 1 Informasi dan Pengaduan',
                'from' => $NOTE_ANTRIAN_FROM_PENGADUAN,
                'type' => $ANTRIAN_TYPE_PENGADUAN,
                'class' => 'primary',
                'color' => 'rgba(255, 99, 132, 0.9)',
                'enable' => $can_access_antrian,
                'order' => 2,
                'max_ongoing' => 3,
                'has_control' => false,
                'forward' => [$ANTRIAN_TYPE_POSBAKUM, $ANTRIAN_TYPE_MEJA1, $ANTRIAN_TYPE_KASIR, $ANTRIAN_TYPE_AC, $ANTRIAN_TYPE_ECOURT, $ANTRIAN_TYPE_BANK],
                'show_in_display' => true,
            ],
            $ANTRIAN_TYPE_MEJA1 => [
                'prefix' => 'B',
                'title_list' => 'Antrian Pendaftaran',
                'title_loket' => 'Loket 3 Pendaftaran',
                'from' => $NOTE_ANTRIAN_PANJAR,
                'type' => $ANTRIAN_TYPE_MEJA1,
                'class' => 'success',
                'color' => 'rgba(255, 205, 86, 0.9)',
                'enable' => $can_access_antrian,
                'order' => 4,
                'max_ongoing' => 3,
                'has_control' => false,
                'forward' => [$ANTRIAN_TYPE_KASIR, $ANTRIAN_TYPE_POS],
                'show_in_display' => true,
            ],
            $ANTRIAN_TYPE_KASIR => [
                'prefix' => 'C',
                'title_list' => 'Antrian Kasir',
                'title_loket' => 'Loket Kasir',
                'from' => $NOTE_ANTRIAN_FROM_KASIR,
                'type' => $ANTRIAN_TYPE_KASIR,
                'class' => 'danger',
                'color' => 'rgba(75, 192, 192, 0.9)',
                'enable' => $can_access_antrian,
                'order' => 6,
                'max_ongoing' => 3,
                'has_control' => false,
                'forward' => [$ANTRIAN_TYPE_MEJA1, $ANTRIAN_TYPE_AC, $ANTRIAN_TYPE_POS, $ANTRIAN_TYPE_BANK],
                'show_in_display' => true,
            ],
            $ANTRIAN_TYPE_POSBAKUM => [
                'prefix' => 'D',
                'title_list' => 'Antrian Posbakum',
                'title_loket' => 'Ruang Posbakum',
                'from' => $NOTE_ANTRIAN_FROM_POSBAKUM,
                'type' => $ANTRIAN_TYPE_POSBAKUM,
                'class' => 'secondary',
                'color' => 'rgba(255, 159, 64, 0.9)',
                'enable' => $can_access_posbakum,
                'order' => 3,
                'max_ongoing' => 3,
                'has_control' => false,
                'forward' => [$ANTRIAN_TYPE_MEJA1, $ANTRIAN_TYPE_POS],
                'show_in_display' => true,
            ],
            $ANTRIAN_TYPE_ECOURT => [
                'prefix' => 'E',
                'title_list' => 'Antrian e-Court',
                'title_loket' => 'Loket e-Court',
                'from' => $NOTE_ANTRIAN_FROM_ECOURT,
                'type' => $ANTRIAN_TYPE_ECOURT,
                'class' => 'warning',
                'color' => 'rgba(54, 162, 235, 0.9)',
                'enable' => $can_access_antrian,
                'order' => 5,
                'max_ongoing' => 3,
                'has_control' => false,
                'forward' => [$ANTRIAN_TYPE_AC],
                'show_in_display' => true,
            ],
            $ANTRIAN_TYPE_AC => [
                'prefix' => 'F',
                'title_list' => 'Antrian Akta Cerai',
                'title_loket' => 'Loket Akta Cerai',
                'from' => $NOTE_ANTRIAN_FROM_AC,
                'type' => $ANTRIAN_TYPE_AC,
                'class' => 'info',
                'color' => 'rgba(153, 102, 255, 0.9)',
                'enable' => $can_access_antrian,
                'order' => 9,
                'max_ongoing' => 5,
                'has_control' => false,
                'forward' => [$ANTRIAN_TYPE_KASIR, $ANTRIAN_TYPE_DISDUKCAPIL],
                'show_in_display' => true,
            ],
            $ANTRIAN_TYPE_POS => [
                'prefix' => 'G',
                'title_list' => 'Antrian Pos',
                'title_loket' => 'Loket Pos',
                'from' => $NOTE_ANTRIAN_FROM_POS,
                'type' => $ANTRIAN_TYPE_POS,
                'class' => 'secondary',
                'color' => 'rgba(255, 159, 64, 0.9)',
                'enable' => $can_access_pos,
                'order' => 7,
                'max_ongoing' => 3,
                'has_control' => false,
                'forward' => [$ANTRIAN_TYPE_MEJA1],
                'show_in_display' => true,
            ],
            $ANTRIAN_TYPE_MEDIASI => [
                'prefix' => 'H',
                'title_list' => 'Antrian Mediasi',
                'title_loket' => 'Ruang Mediasi',
                'type' => $ANTRIAN_TYPE_MEDIASI,
                'class' => 'primary',
                'color' => 'rgba(201, 203, 207, 0.9)',
                'enable' => $can_access_mediasi,
                'order' => 11,
                'max_ongoing' => 3,
                'has_control' => false,
                'show_in_display' => true,
            ],
            $ANTRIAN_TYPE_BANK => [
                'prefix' => 'J',
                'title_list' => 'Antrian Bank',
                'title_loket' => 'Loket Bank',
                'from' => $NOTE_ANTRIAN_FROM_BANK,
                'type' => $ANTRIAN_TYPE_BANK,
                'class' => 'secondary',
                'color' => 'rgba(255, 159, 64, 0.9)',
                'enable' => $can_access_bank,
                'order' => 8,
                'max_ongoing' => 3,
                'has_control' => false,
                'forward' => [$ANTRIAN_TYPE_KASIR, $ANTRIAN_TYPE_POSBAKUM],
                'show_in_display' => true,
            ],
            $ANTRIAN_TYPE_DISDUKCAPIL => [
                'prefix' => 'K',
                'title_list' => 'Antrian Disdukcapil',
                'title_loket' => 'Loket Disdukcapil',
                'from' => $NOTE_ANTRIAN_FROM_DISDUKCAPIL,
                'type' => $ANTRIAN_TYPE_DISDUKCAPIL,
                'class' => 'secondary',
                'color' => 'rgba(255, 159, 64, 0.9)',
                'enable' => $can_access_disdukcapil,
                'order' => 10,
                'max_ongoing' => 3,
                'has_control' => false,
                'forward' => [],
                'show_in_display' => true,
            ],
            $ANTRIAN_TYPE_SIDANGR1 => [
                'prefix' => 'R1',
                'title_list' => 'Antrian Ruang Sidang 1',
                'title_loket' => 'R. Sidang 1',
                'ruang' => 1,
                'type' => $ANTRIAN_TYPE_SIDANGR1,
                'class' => 'muted',
                'color' => 'rgb(128, 237, 153, 0.9)',
                'enable' => $can_access_antrian || $can_access_mediasi,
                'order' => 1,
                'max_ongoing' => 1,
                'has_control' => false,
                'show_in_display' => true,
                'has_ruang' => true,
            ],
            $ANTRIAN_TYPE_SIDANGR2 => [
                'prefix' => 'R2',
                'title_list' => 'Antrian Ruang Sidang 2',
                'title_loket' => 'R. Sidang 2',
                'ruang' => 2,
                'type' => $ANTRIAN_TYPE_SIDANGR2,
                'class' => 'muted',
                'color' => 'rgb(87, 204, 153, 0.9)',
                'enable' => $can_access_antrian || $can_access_mediasi,
                'order' => 2,
                'max_ongoing' => 1,
                'has_control' => false,
                'show_in_display' => true,
                'has_ruang' => true,
            ],
            $ANTRIAN_TYPE_SIDANGR3 => [
                'prefix' => 'R3',
                'title_list' => 'Antrian Ruang Sidang 3',
                'title_loket' => 'R. Sidang 3',
                'ruang' => 3,
                'type' => $ANTRIAN_TYPE_SIDANGR3,
                'class' => 'muted',
                'color' => 'rgb(87, 204, 100, 0.9)',
                'enable' => $can_access_antrian || $can_access_mediasi,
                'order' => 3,
                'max_ongoing' => 1,
                'has_control' => false,
                'show_in_display' => false,
                'has_ruang' => true,
                'active' => false,
            ],
            $ANTRIAN_TYPE_ELITIGASI => [
                'prefix' => 'R5',
                'title_list' => 'Antrian e-Litigasi',
                'title_loket' => 'e-Litigasi',
                'ruang' => 5,
                'type' => $ANTRIAN_TYPE_ELITIGASI,
                'class' => 'muted',
                'color' => 'rgb(87, 204, 100, 0.9)',
                'enable' => $can_access_antrian,
                'order' => 4,
                'max_ongoing' => 1,
                'has_control' => false,
                'show_in_display' => false,
            ],
            $ANTRIAN_TYPE_SIDANG_KELILING1 => [
                'prefix' => 'R6',
                'title_list' => 'Antrian Sidang Keliling 1',
                'title_loket' => 'Sid. Keliling 1',
                'ruang' => 6,
                'type' => $ANTRIAN_TYPE_SIDANG_KELILING1,
                'class' => 'muted',
                'color' => 'rgb(87, 204, 100, 0.9)',
                'enable' => $can_access_antrian || $can_access_mediasi,
                'order' => 5,
                'max_ongoing' => 1,
                'has_control' => false,
                'show_in_display' => false,
                'has_ruang' => false,
                'active' => true,
            ],
            $ANTRIAN_TYPE_SIDANG_KELILING2 => [
                'prefix' => 'R7',
                'title_list' => 'Antrian Sidang Keliling 2',
                'title_loket' => 'Sid. Keliling 2',
                'ruang' => 7,
                'type' => $ANTRIAN_TYPE_SIDANG_KELILING2,
                'class' => 'muted',
                'color' => 'rgb(87, 204, 100, 0.9)',
                'enable' => $can_access_antrian || $can_access_mediasi,
                'order' => 6,
                'max_ongoing' => 1,
                'has_control' => false,
                'show_in_display' => false,
                'has_ruang' => false,
                'active' => true,
            ],
            $ANTRIAN_TYPE_IKRAR1 => [
                'prefix' => 'I1',
                'type' => $ANTRIAN_TYPE_SIDANGR1,
                'title_list' => 'Antrian Ikrar Ruang Sidang 1',
                'title_loket' => 'R. Sidang 1',
                'color' => 'rgb(56, 163, 165, 0.9)',
            ],
            $ANTRIAN_TYPE_IKRAR2 => [
                'prefix' => 'I2',
                'type' => $ANTRIAN_TYPE_SIDANGR2,
                'title_list' => 'Antrian Ikrar Ruang Sidang 2',
                'title_loket' => 'R. Sidang 2',
                'color' => 'rgb(34, 87, 122, 0.9)',
            ],
            $ANTRIAN_TYPE_IKRAR3 => [
                'prefix' => 'I3',
                'type' => $ANTRIAN_TYPE_SIDANGR3,
                'title_list' => 'Antrian Ikrar Ruang Sidang 3',
                'title_loket' => 'R. Sidang 3',
                'color' => 'rgb(34, 87, 100, 0.9)',
            ],
            $ANTRIAN_TYPE_IKRAR_KELILING1 => [
                'prefix' => 'I6',
                'type' => $ANTRIAN_TYPE_SIDANG_KELILING1,
                'title_list' => 'Antrian Sidang Keliling 1',
                'title_loket' => 'Sid. Keliling 1',
                'color' => 'rgb(34, 87, 100, 0.9)',
            ],
            $ANTRIAN_TYPE_IKRAR_KELILING2 => [
                'prefix' => 'I7',
                'type' => $ANTRIAN_TYPE_SIDANG_KELILING2,
                'title_list' => 'Antrian Sidang Keliling 2',
                'title_loket' => 'Sid. Keliling 2',
                'color' => 'rgb(34, 87, 100, 0.9)',
            ],
            $ANTRIAN_TYPE_PRIORITY => [
                'prefix' => 'PRI',
                'type' => $ANTRIAN_TYPE_PRIORITY,
                'title_list' => 'Antrian Prioritas',
                'color' => 'rgb(34, 87, 25, 0.9)',
                'show_in_display' => false,
            ],
            $ANTRIAN_TYPE_ONLINE => [
                'prefix' => 'ON',
                'type' => $ANTRIAN_TYPE_ONLINE,
                'title_list' => 'Antrian Prioritas',
                'color' => 'rgb(34, 87, 50, 0.9)',
                'show_in_display' => false,
            ],
            $CI->config->item('ANTRIAN_TYPE_SIDANG') => []
        ];
        return $type ? $config[$type] : $config;
    }
}

/**
 * mengambil semua ruang sidang yang aktif
 */
if (!function_exists('get_active_ruang_sidang')) {
    function get_active_ruang_sidang($hasRuangOnly = true)
    {
        $configs = [];
        foreach (get_queue_config() as $conf) {
            if (isset($conf['ruang']) && (!isset($conf['active']) || $conf['active'] === true) && ($hasRuangOnly ? $conf['show_in_display'] && isset($conf['has_ruang']) && $conf['has_ruang'] === true : true)) {
                $configs[] = $conf;
            }
        }

        usort($configs, function ($item1, $item2) {
            if (!isset($item1['order']) || !isset($item2['order'])) {
                return 0;
            }
            return $item1['order'] < $item2['order'] ? -1 : 1;
        });

        return $configs;
    }
}

if (!function_exists('get_queue_type')) {
    function get_queue_type($key = null)
    {
        $types = [];
        foreach (get_queue_config() as $type => $config) {
            if (isset($config['prefix'])) {
                $types[] = $key ? $config[$key] : $type;
            }
        }
        return $types;
    }
}

if (!function_exists('get_csrf_nonce')) {
    function get_csrf_nonce()
    {
        $CI = get_instance();

        $CI->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $CI->session->set_flashdata('csrfkey', $key);
        $CI->session->set_flashdata('csrfvalue', $value);

        return [$key => $value];
    }
}

if (!function_exists('valid_csrf_nonce')) {
    function valid_csrf_nonce()
    {
        $CI = get_instance();

        $csrfkey = $CI->input->post($CI->session->flashdata('csrfkey'));
        if ($csrfkey && $csrfkey === $CI->session->flashdata('csrfvalue')) {
            return TRUE;
        }
        return FALSE;
    }
}

if (!function_exists('duplicate_queue')) {
    function duplicate_queue($antrian, $target, $ket = '', $status = null, $duplicateFile = true)
    {
        $CI = get_instance();
        if ($status === null) {
            $status = $CI->config->item('antrian_status_init');
        }
        return [
            'no_antrian' => $antrian->no_antrian,
            'tanggal_antrian' => $antrian->tanggal_antrian,
            'perkara_id' => $antrian->perkara_id,
            'nomor_perkara' => $antrian->nomor_perkara,
            'ruang_id' => $antrian->ruang_id,
            'nama' => $antrian->nama,
            'nik' => $antrian->nik,
            'status' => $status,
            'jam_ambil' => date('H:i:s'),
            'ket' => $ket ?: $antrian->ket,
            'tipe' => $target,
            'petugas_id' => $antrian->petugas_id,
            'photo' => $duplicateFile ? $antrian->photo : null,
            'file_identitas' => $duplicateFile ? $antrian->file_identitas : null,
        ];
    }
}

if (!function_exists('print_queue')) {
    function print_queue($data)
    {
        try {
            // $connector = new Escpos\PrintConnectors\DummyPrintConnector();
            // $connector = new Escpos\PrintConnectors\FilePrintConnector("php://stdout");
            // $connector = new Escpos\PrintConnectors\FilePrintConnector(__DIR__ . "/../logs/tes.bin");

            if (ENABLE_BLUETOOTH_PRINTER) {
                $connector = new Escpos\PrintConnectors\RawbtPrintConnector();
            } else {
                $connector = new Escpos\PrintConnectors\WindowsPrintConnector(PRINTER_SMB);
            }

            include APPPATH . 'libraries/MyPrinter.php';
            $printer = new MyPrinter($connector);

            /* Initialize */
            $printer->initialize();

            $printer->setJustification(Escpos\Printer::JUSTIFY_CENTER);

            /* Include top image if set & available */
            $logo = __DIR__ . "/../../assets/images/logo_transparent.png";
            if (file_exists($logo)) {
                $printer->bitImageColumnFormat(Escpos\EscposImage::load($logo));
            }

            $printer->setText(SATKER_NAME, Escpos\Printer::MODE_DOUBLE_HEIGHT);

            $printer->setText(format_date(date('Y-m-d H:i:s'), "EEEE, dd MMMM yyyy HH:mm"));

            $printer->setText(get_queue_config($data['tipe'])['title_loket'], Escpos\Printer::MODE_EMPHASIZED);

            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text($data['no_antrian']);
            $printer->setEmphasis(false);
            $printer->feed();

            if ($data['nomor_perkara']) {
                $printer->setText($data['nomor_perkara'], Escpos\Printer::MODE_DOUBLE_HEIGHT);
            }

            $printer->setText('Terima Kasih', Escpos\Printer::UNDERLINE_SINGLE, 2);

            $printer->cut();
            $printer->close();

            return true;
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return $e->getMessage();
        }
    }
}
