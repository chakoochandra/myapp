<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('check_auth')) {
    function check_auth()
    {
        foreach (func_get_args() as $isvalid) {
            if ($isvalid === true) {
                return $isvalid;
            }
        }

        $message = 'Anda tidak memiliki akses untuk melakukan aksi tersebut';

        $CI = get_instance();
        if ($CI->input->is_ajax_request()) {
            return [
                'redirect' => base_url('site'),
                'message' => $message,
                'status' => false,
            ];
        }

        $CI->toastr->error($message);
        redirect('site');
    }
}

if (!function_exists('is_kepegawaian')) {
    function is_kepegawaian($id = null)
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        $user = $CI->ion_auth->user($id)->row();
        return $user->active && ($CI->ion_auth_model->in_group([$CI->config->item('group_kepegawaian')], $user->id) || is_kasub_kepegawaian());
    }
}

if (!function_exists('is_kasub_kepegawaian')) {
    function is_kasub_kepegawaian($id = null)
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        $user = $CI->ion_auth->user($id)->row();
        if ($user->active && $user->jabatan_id == $CI->config->item('jabatan_kasub_kepegawaian')) {
            return true;
        }

        $CI->load->model('pegawai/Pegawai_Model', 'pegawai');
        return $user->active && !$CI->pegawai->findUserJabatan($CI->config->item('jabatan_kasub_kepegawaian')) && $CI->ion_auth_model->in_group([$CI->config->item('group_plt_kasub_kepegawaian')], $user->id);
    }
}

if (!function_exists('is_panmud')) {
    function is_panmud($id = null)
    {
        if (!get_instance()->ion_auth->logged_in()) {
            return false;
        }
        $user = get_instance()->ion_auth->user($id)->row();
        return $user && $user->active && in_array($user->jabatan_id, [get_instance()->config->item('jabatan_panmud_hukum'), get_instance()->config->item('jabatan_panmud_gugatan'), get_instance()->config->item('jabatan_panmud_permohonan'), get_instance()->config->item('group_administrator')]);
    }
}

if (!function_exists('has_sidang')) {
    function has_sidang($id = null)
    {
        if (!get_instance()->ion_auth->logged_in()) {
            return false;
        }
        $user = get_instance()->ion_auth->user($id)->row();
        return $user && $user->active && in_array($user->jabatan_id, [get_instance()->config->item('jabatan_ketua'), get_instance()->config->item('jabatan_wakil_ketua'), get_instance()->config->item('jabatan_hakim'), get_instance()->config->item('jabatan_panitera'), get_instance()->config->item('jabatan_panmud_hukum'), get_instance()->config->item('jabatan_panmud_gugatan'), get_instance()->config->item('jabatan_panmud_permohonan'), get_instance()->config->item('jabatan_pp'), get_instance()->config->item('group_administrator')]);
    }
}

if (!function_exists('is_atasan_cuti')) {
    function is_atasan_cuti($id = null)
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        $CI->load->model('pegawai/Pegawai_Model', 'pegawai');

        $user = $CI->ion_auth->user($id)->row();

        //apakah pejabat yang bersangkutan?
        if ($user->active && in_array($user->jabatan_id, jabatan_atasan_cuti())) {
            return true;
        }

        //apakah plt ketua?
        if ($user->active && !$CI->pegawai->findUserJabatan($CI->config->item('jabatan_ketua')) && $CI->ion_auth_model->in_group([$CI->config->item('group_plt_ketua')], $user->id)) {
            return true;
        }

        //apakah plt wakil ketua?
        if ($user->active && !$CI->pegawai->findUserJabatan($CI->config->item('jabatan_wakil_ketua')) && $CI->ion_auth_model->in_group([$CI->config->item('group_plt_wakil_ketua')], $user->id)) {
            return true;
        }

        //apakah plt panitera?
        if ($user->active && !$CI->pegawai->findUserJabatan($CI->config->item('jabatan_panitera')) && $CI->ion_auth_model->in_group([$CI->config->item('group_plt_panitera')], $user->id)) {
            return true;
        }

        //apakah plt sekretaris?
        return $user->active && !$CI->pegawai->findUserJabatan($CI->config->item('jabatan_sekretaris')) && $CI->ion_auth_model->in_group([$CI->config->item('group_plt_sekretaris')], $user->id);
    }
}

if (!function_exists('is_pejabat_cuti')) {
    function is_pejabat_cuti($id = null)
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        $user = $CI->ion_auth->user($id)->row();
        if ($user->active && $user->jabatan_id == $CI->config->item('jabatan_ketua')) {
            return true;
        }

        $CI->load->model('pegawai/Pegawai_Model', 'pegawai');
        return $user->active && !$CI->pegawai->findUserJabatan($CI->config->item('jabatan_ketua')) && $CI->ion_auth_model->in_group([$CI->config->item('group_plt_ketua')], $user->id);
    }
}

if (!function_exists('get_structural_group')) {
    function get_structural_group()
    {
        return [0, 2, 3];
    }
}

if (!function_exists('is_operator')) {
    function is_operator($id = null)
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        $user = $CI->ion_auth->user($id)->row();
        return $user->active && ($CI->ion_auth_model->in_group([$CI->config->item('group_operator'), $CI->config->item('group_administrator')], $user->id));
    }
}

if (!function_exists('is_tekre')) {
    function is_tekre($id = null)
    {
        $CI = get_instance();
        if (!$CI->ion_auth->logged_in()) {
            return false;
        }

        $user = $CI->ion_auth->user($id)->row();
        return $user->active && ($CI->ion_auth_model->in_group([$CI->config->item('group_tekre'), $CI->config->item('group_administrator')], $user->id));
    }
}
