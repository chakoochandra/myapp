<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Site extends Core_Controller
{
	function index()
	{
		redirect('ck/bht');
	}

	public function check_gateway()
	{
		return $this->set_content_type(hit_api([
			'endpoint' => DIALOGWA_API_URL . '/session/' . DIALOGWA_SESSION,
			'type' => 'get',
			'data' => null,
			'token' => DIALOGWA_TOKEN
		]));
	}
}
