<?php
defined('BASEPATH') or exit('No direct script access allowed');

class My_hooks
{

	public function hide_particles()
	{
		$CI = &get_instance();
		// $CI->vars['showParticles'] = true;
	}

	/**
	 * Auto-assign "Pegawai" group (id = 99) to logged-in users without groups
	 * This ensures every user has at least one group for proper menu access
	 */
	public function auto_assign_pegawai_group()
	{
		$CI = &get_instance();

		// Check if user is logged in
		if ($CI->ion_auth->logged_in()) {
			$user = $CI->ion_auth->user()->row();

			if ($user) {
				// Get user's groups
				$user_groups = $CI->ion_auth->get_users_groups($user->id)->result();

				// If user has no groups, assign Pegawai group (id = 99)
				if (empty($user_groups)) {
					$CI->ion_auth->add_to_group(99, $user->id);
				}
			}
		}
	}
}
