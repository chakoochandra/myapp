<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bht extends Core_Controller
{
	private $_canEditPanmud;
	private $_canEdit;

	public function __construct()
	{
		parent::__construct();

		$this->_ensure_trans_minutation_table();
		$this->_ensure_wa_bht_target_config();

		$this->_canEditPanmud = is_panmud() || is_operator();
		$this->_canEdit = $this->ion_auth->logged_in();
		$this->load->model('ck/Bht_Model', 'disposisi');
		$this->model = $this->disposisi;

		$this->indexTitle = 'Kontrol Tanggal BHT';
		$this->indexSubtitle = 'Modul ini menampilkan progress rencana BHT.';
		$this->indexIcon = 'fa-solid fa-clipboard-check';
		$this->indexView = 'ck/bht/index';
		$this->module_id = 'monitoring_rencana_bht';
	}

	private function _ensure_trans_minutation_table()
	{
		$db = $this->db;
		if (!$db->table_exists('trans_minutation')) {
			$db->query("
				CREATE TABLE `trans_minutation` (
					`perkara_id` BIGINT(11) NOT NULL,
					`tanggal_pp_setor` DATE NULL DEFAULT NULL COMMENT 'Tanggal PP setor instrumen',
					`tanggal_jsp_terima` DATE NULL DEFAULT NULL COMMENT 'Tanggal JS/JSP terima instrumen',
					`tanggal_panmudg_terima` DATE NULL DEFAULT NULL,
					`tanggal_serah_ke_minut` DATE NULL DEFAULT NULL,
					`tanggal_serah_ke_ac` DATE NULL DEFAULT NULL,
					`tanggal_serah_ke_arsip` DATE NULL DEFAULT NULL,
					`tanggal_upload_ecourt` DATE NULL DEFAULT NULL,
					`tanggal_tte_ecourt` DATE NULL DEFAULT NULL,
					`tanggal_upload_ecourt_verzet` DATE NULL DEFAULT NULL,
					`tanggal_tte_ecourt_verzet` DATE NULL DEFAULT NULL,
					`tanggal_rencana_bht` DATE NULL DEFAULT NULL,
					PRIMARY KEY (`perkara_id`)
				)
				COLLATE='latin1_swedish_ci'
				ENGINE=InnoDB
				ROW_FORMAT=DYNAMIC
			");
			log_message('info', 'trans_minutation table created');
		}
	}

	private function _ensure_wa_bht_target_config()
	{
		$db = $this->db;
		$query = $db->get_where('tmst_configs', ['key' => 'WA_BHT_TARGET']);
		if ($query->num_rows() === 0) {
			$db->insert('tmst_configs', [
				'key' => 'WA_BHT_TARGET',
				'value' => '',
				'category' => 5,
				'note' => 'string. no whatsapp target untuk notifikasi BHT'
			]);
			log_message('info', 'WA_BHT_TARGET config inserted');
		}
	}

	protected function prepare_index($options = [])
	{
		$this->indexLayout = 'layout_no_sidebar';

		$this->vars['canEdit'] = $this->_canEdit;
		$this->vars['canEditPanmud'] = $this->_canEditPanmud;

		parent::prepare_index($this->vars);
	}

	function send_notif_rencana_bht()
	{
		$useQueue = false;
		$today = date('Y-m-d');
		$tomorrow = date('Y-m-d', strtotime('+1 day'));
		$dayAfterTomorrow = date('Y-m-d', strtotime('+2 day'));

		$result = $this->disposisi->getRencanaBhtByDate($today, $tomorrow, $dayAfterTomorrow);

		if (!empty($result)) {
			$text = "📅 *Kontrol Tanggal BHT*\n";
			foreach ($result as $row) {
				$formattedDate = format_date($row->tanggal_rencana_bht, "EEEE, dd MMMM yyyy");
				$text .= "\n*{$formattedDate} ({$row->jumlah} perkara)*\n";
				foreach ($row->perkaras as $nomorPerkara) {
					$text .= "• {$nomorPerkara}\n";
				}
			}
			$text .= "\n" . notif_footer();

			$targets = (is_development() ? cleanse_phone_number(WA_TEST_TARGET) : cleanse_phone_number(WA_BHT_TARGET));
			$x = 0;
			foreach ($targets as $no) {
				$waData = [
					'type' => 'Rencana BHT',
					'target' => $no,
					'text' => $text,
				];
				if ($useQueue) {
					queue_wa_message($waData);
				} else {
					send_wa($waData);
					sleep(10);
				}
				$x++;
				$this->send_stream_data(['progress' => round($x * 100 / count($targets)), 'no' => $x, 'message' => 'Notifikasi sudah dalam proses pengiriman', 'status' => true]);
			}

			$this->send_stream_data(['progress' => 100, 'message' => $targets > 0 ? "Selesai, {$x} notifikasi sudah dalam proses pengiriman" : "WA_TEST_TARGET dan/atau WA_BHT_TARGET belum diset", 'status' => $x >= 0]);
		} else {
			$this->send_stream_data([
				'progress' => 100,
				'status' => true,
				'message' => 'Tidak ada rencana BHT untuk dikirim',
			]);
		}
	}
}
