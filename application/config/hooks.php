<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['post_controller_constructor'][] = array(
	'class'    => 'Configs',
	'function' => 'get_configs',
	'filename' => 'Configs.php',
	'filepath' => 'hooks'
);

/*
 | Slow query logger: logs queries exceeding threshold (default 100ms) after controller runs.
 | Implemented by application/hooks/SlowQueryLogger.php::log()
 */
$hook['post_controller'][] = array(
	'class'    => 'SlowQueryLogger',
	'function' => 'log',
	'filename' => 'SlowQueryLogger.php',
	'filepath' => 'hooks'
);

/*
 | Auto-assign Pegawai group to users without groups
 | Implemented by application/hooks/My_hooks.php::auto_assign_pegawai_group()
 */
$hook['post_controller_constructor'][] = array(
	'class'    => 'My_hooks',
	'function' => 'auto_assign_pegawai_group',
	'filename' => 'My_hooks.php',
	'filepath' => 'hooks'
);

// $hook['post_controller'][] = array(
// 	'class'    => 'My_hooks',
// 	'function' => 'hide_particles',
// 	'filename' => 'My_hooks.php',
// 	'filepath' => 'hooks'
// );
