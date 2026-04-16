<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo isset($title) ? $title : ($this->config->item('app_name') ?: 'JOSS') ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="title" content="<?php echo isset($title) ? $title : ($this->config->item('app_name') ?: 'JOSS') ?>">
	<meta name="author" content="Chako">
	<meta name="description" content="<?php echo isset($title) ? $title : ($this->config->item('app_name') ?: 'JOSS') ?>">
	<meta name="keywords" content="<?php echo isset($title) ? $title : ($this->config->item('app_name') ?: 'JOSS') ?>">

	<link rel="shortcut icon" href="<?php echo asset_url('assets/images/joss.png') ?>" type="image/png">

	<?php
	$hasAssets      = isset($assets) && is_array($assets);
	$legacyFallback = !$hasAssets && empty($lightAssets);
	$useICheck      = ($hasAssets && !empty($assets['icheck']))     || $legacyFallback;
	$useJqueryUI    = ($hasAssets && !empty($assets['jquery_ui']))  || $legacyFallback;
	$useBusyLoad    = ($hasAssets && !empty($assets['busy_load']))  || $legacyFallback;
	$useMoment      = ($hasAssets && !empty($assets['moment']))     || $legacyFallback;

	// Get theme CSS files for preloading
	$themeCssFiles = [];
	foreach (get_available_themes() as $theme) {
		if (isset($theme['cssFile'])) {
			$themeCssFiles[] = $theme['cssFile'];
		}
	}
	?>
	<link rel="stylesheet" href="<?php echo asset_url('assets/plugins/adminlte4/dist/css/adminlte.css') ?>">
	<?php foreach ($themeCssFiles as $cssFile): ?>
		<link rel="stylesheet" href="<?php echo asset_url('assets/plugins/bootstrap/custom/' . $cssFile) ?>" id="theme-css-<?php echo str_replace('.css', '', $cssFile); ?>" disabled style="display: none;">
	<?php endforeach; ?>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="<?php echo asset_url('assets/plugins/font-awesome/css/font-awesome.min.css') ?>">
	<?php if ($useJqueryUI): ?>
		<link rel="stylesheet" href="<?php echo asset_url('assets/plugins/jquery-ui/jquery-ui.min.css') ?>">
	<?php endif; ?>
	<link rel="stylesheet" href="<?php echo asset_url('assets/css/glow.css') ?>">
	<link rel="stylesheet" href="<?php echo asset_url('assets/css/app.css') ?>">
	<link rel="stylesheet" href="<?php echo asset_url('assets/css/custom-themes.css') ?>">

	<script src="<?php echo asset_url('assets/plugins/jquery/jquery.min.js') ?>"></script>
	<script type="text/javascript">
		var $ = jQuery.noConflict();
	</script>

	<!-- Minimal moment shim: prevents ReferenceError if moment is gated off or loads late -->
	<script>
		(function() {
			if (typeof window.moment === 'undefined') {
				window.moment = function(input) {
					var d = (input instanceof Date) ? new Date(input) : new Date(input || Date.now());
					return {
						startOf: function(unit) {
							if (unit === 'day') {
								d.setHours(0, 0, 0, 0);
							}
							return this;
						},
						day: function() {
							return d.getDay();
						},
						add: function(n, unit) {
							if (unit === 'day' || unit === 'days') {
								d = new Date(d.getTime() + n * 86400000);
							}
							return this;
						},
						subtract: function(n, unit) {
							return this.add(-n, unit);
						},
						isSameOrBefore: function(other) {
							var e = (other && typeof other.toDate === 'function') ? other.toDate() : new Date(other);
							return d.getTime() <= e.getTime();
						},
						format: function() {
							try {
								return d.toLocaleString('id-ID', {
									weekday: 'long',
									day: '2-digit',
									month: 'long',
									year: 'numeric',
									hour: '2-digit',
									minute: '2-digit'
								});
							} catch (e) {
								return d.toISOString();
							}
						},
						diff: function(other, unit) {
							var o = (other && typeof other.toDate === 'function') ? other.toDate() : new Date(other);
							var ms = d - o;
							if (unit === 'days') return Math.round(ms / 86400000);
							return ms;
						},
						toDate: function() {
							return d;
						}
					};
				};
			}
		})();
	</script>

	<?php if ($useJqueryUI): ?>
		<script src="<?php echo asset_url('assets/plugins/jquery-ui/jquery-ui.min.js') ?>" defer></script>
	<?php endif; ?>
	<?php if ($useBusyLoad): ?>
		<!-- busy-load -->
		<link rel="stylesheet" href="<?php echo asset_url('assets/plugins/busy-load/busy-load.min.css') ?>">
		<script src="<?php echo asset_url('assets/plugins/busy-load/busy-load.min.js') ?>"></script>
	<?php endif; ?>
	<?php //if ($useMoment): 
	?>
	<!-- moment -->
	<script src="<?php echo asset_url('assets/plugins/moment/moment.js') ?>"></script>
	<script src="<?php echo asset_url('assets/plugins/moment/locale/id.js') ?>"></script>
</head>

<body class="<?php echo get_layout_classes('mode-layout-plain') ?>">
	<?php $this->benchmark->mark('code_start') ?>

	<?php $this->load->view('partials/_navbar', array('hasSidebar' => false)); ?>

	<!-- <div class="card container-main d-flex justify-content-center" style="background: transparent;"> -->
	<div class="container-main d-flex justify-content-center mt-2">
		<?php $showLogo = isset($showLogo) ? $showLogo : false ?>
		<?php if ($showLogo) : ?>
			<img src="<?php echo asset_url('assets/images/icon.png') ?>" height="100px" alt="Logo <?php echo ($this->config->item('satker_name') ?: 'JOSS') ?>" class="brand-image mt-3">
		<?php endif ?>
		<?php if (isset($showTitle) && $showTitle) : ?>
			<span class="h4">
				<?php echo strtoupper($title) ?>
			</span>
		<?php endif ?>

		<?php $this->load->view($main_body) ?>
	</div>

	<?php $this->load->view('partials/_footer', ['isPrivate' => !isset($isPrivate) || $isPrivate]) ?>
	<?php $this->benchmark->mark('code_end') ?>
</body>

</html>