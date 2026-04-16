<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation
{
    protected $CI;

    function __construct()
    {
        parent::__construct();
        $this->CI = &get_instance();
    }

    /**
     * Callback function. Checks if the length of the user's input
     * conforms to minimum and maximum character length requirements
     *
     * @param  string
     * @param  int
     * @param  int
     * @return bool
     */
    // function verify_length($value, $min, $max)
    // {
    //     $length = strlen($value);

    //     if ($length <= $max && $length >= $min) {
    //         return TRUE;
    //     } elseif ($length < $min) {
    //         $this->form_validation->set_message('verify_length', '%s harus mengandung minimal ' . $min . ' karakter');
    //         return FALSE;
    //     } elseif ($length > $max) {
    //         $this->form_validation->set_message('verify_length', '%s harus mengandung maksimal ' . $max . ' karakter');
    //         return FALSE;
    //     }
    // }

    function verify_identity($value)
    {
        // if (preg_match('/^[a-z]\w{2,23}[^_]$/i', $value)) {
        // if (preg_match('/^\w{5,}$/', $value)) { // \w equals "[0-9A-Za-z_]"
        if (preg_match('/^[a-z0-9]{5,}$/', $value)) { // for english chars + numbers only
            return TRUE;
        }

        $this->CI->form_validation->set_message('verify_identity', '%s hanya boleh mengandung huruf kecil dan angka');
        return FALSE;
    }

    function verify_nik($value)
    {
        if (strlen($value) == 16) {
            return TRUE;
        }

        $this->CI->form_validation->set_message('verify_nik', '%s harus 16 digit');
        return FALSE;
    }

    function verify_nip($value)
    {
        if (strlen($value) == 18) {
            return TRUE;
        }

        $this->CI->form_validation->set_message('verify_nip', '%s harus 18 digit');
        return FALSE;
    }

    function verify_captcha($value)
    {
        if ($value) {
            // First, delete old captchas
            $expiration = time() - 7200; // Two hour limit
            $this->CI->db->where('captcha_time < ', $expiration)->delete('captcha');

            // Then see if a captcha exists:
            $sql = 'SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?';
            $binds = array($value, $this->CI->input->ip_address(), $expiration);
            $query = $this->CI->db->query($sql, $binds);
            $row = $query->row();

            if ($row->count > 0) {
                return TRUE;
            }

            $this->CI->form_validation->set_message('verify_captcha', '%s tidak sesuai');
            return FALSE;
        }
    }

    function compare_date()
    {
        $tanggal_awal = strtotime($_POST['tanggal_awal']);
        $tanggal_akhir = strtotime($_POST['tanggal_akhir']);

        if ($tanggal_akhir >= $tanggal_awal) {
            return True;
        }

        $this->CI->form_validation->set_message('compare_date', '%s tidak boleh sebelum tanggal mulai cuti.');
        return False;
    }

    function check_pending_cuti($value)
    {
        $this->CI->load->model('pegawai/Cuti_Model', 'cuti');

        if (!($cutis = $this->CI->cuti->find([
            $this->CI->config->item('tbl_cuti') . ".id <> {$_POST['id_cuti']}",
            ['user_id' => $value],
            '(status IN (' . implode(', ', status_cuti_on_process()) . '))',
        ]))) {
            return true;
        }

        $this->CI->form_validation->set_message('check_pending_cuti', $cutis[0]->nama_pegawai . ' sudah memiliki proses pengajuan cuti');
        return false;
    }

    function check_sisa_cuti($value)
    {
        $this->CI->load->model('pegawai/Cuti_Model', 'cuti');

        if ($_POST['id_cuti']) {
            $cuti = $this->CI->cuti->findOne($_POST['id_cuti']);
            $id_jenis_cuti = $cuti->jenis_cuti_id;
        } else {
            $id_jenis_cuti = $_POST['jenis_cuti_id'];
        }

        //cuti sakit dan CAP boleh melebihi jatah tahunan (tunjangan dipotong)
        if ($id_jenis_cuti != $this->CI->config->item('cuti_tahunan')) {
            return true;
        }

        $lama_cuti = count_working_days($_POST['tanggal_awal'], $value);
        if ($lama_cuti <= ($sisa_cuti = $this->CI->cuti->countRemainder($_POST['user_id'], $id_jenis_cuti) ?: 0)) {
            return true;
        }

        $this->CI->load->model('pegawai/Pegawai_Model', 'pegawai');
        $this->CI->load->model('Jenis_pegawai/Cuti_Model', 'jenis_cuti');

        $user = $this->CI->pegawai->findOne($_POST['user_id']);
        $jenisCuti = $this->CI->jenis_cuti->findOne($id_jenis_cuti)->jenis_cuti;
        $this->CI->form_validation->set_message('check_sisa_cuti', "{$user->nama_pegawai} hanya memiliki sisa $jenisCuti $sisa_cuti hari");
        return false;
    }

    function check_overlap_cuti($value)
    {
        $this->CI->load->model('pegawai/Cuti_Model', 'cuti');

        if (!($cutis = $this->CI->cuti->isOverlap($value, $_POST['tanggal_awal'], $_POST['tanggal_akhir'], $_POST['id_cuti']))) {
            return true;
        }

        $this->CI->form_validation->set_message('check_overlap_cuti', $cutis[0]->nama_pegawai . ' sudah memiliki proses pengajuan cuti antara tanggal ' . format_date($_POST['tanggal_awal']) . ' dan ' . format_date($_POST['tanggal_akhir']));
        return false;
    }

    function check_misc_year_exists($value)
    {
        $this->CI->load->model('pegawai/EmployeeData_Model', 'misc');

        if (!$this->CI->misc->count_list_filtered([
            'type' => $_POST['type'],
            'user_id' => $value,
            'year' => $_POST['year'],
            $this->CI->config->item('tbl_misc_pegawai') . ".id <> {$_POST['id_misc']}" => null
        ])) {
            return true;
        }

        $this->CI->form_validation->set_message('check_misc_year_exists', "Data tahun {$_POST['year']} sudah ada");
        return false;
    }

    function check_jabatan_jobdesc_exists($value)
    {
        $this->CI->load->model('pegawai/Jobdesc_Model', 'jobdesc');

        if (!($jobdesc = $this->CI->jobdesc->isJabatanExists($value, $_POST['id_jobdesc']))) {
            return true;
        }

        $this->CI->form_validation->set_message('check_jabatan_jobdesc_exists',  'Uraian tugas ' . $jobdesc[0]->nama_jabatan . ' sudah ada');
        return false;
    }
}
