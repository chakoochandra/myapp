<nav class="<?php echo get_layout_classes((!isset($navbarClass) ? 'navbar' : $navbarClass)) ?> <?php echo $this->session->userdata('disableApiRequest') ? 'bg-red' : '' ?>">
	<div class="container-fluid mb-0">
		<ul class="navbar-nav">
			<?php if (isset($hasSidebar) && $hasSidebar && is_local_ip()): ?>
				<li class="nav-item">
					<a class="nav-link px-2" href="#" data-lte-toggle="sidebar" role="button">
						<i class="fa fa-bars"></i>
					</a>
				</li>
			<?php else: ?>
				<div class="sidebar-brand" style="height: 36px; border: none;">
					<a href="<?php echo base_url('/') ?>" class="brand-link" style="align-items: end;">
						<img src="<?php echo asset_url('assets/images/joss.png') ?>" alt="Logo <?php echo APP_SHORT_NAME ?>" class="brand-image">
						<span class="brand-text fw-bolder fs-2 ms-0" style="line-height: .9 !important"><?php echo APP_SHORT_NAME ?></span>
					</a>
				</div>
			<?php endif ?>

			<?php /*if (($antrianIndex = array_search_key_index('title', 'Antrian', $menus))) {
            $configs = get_queue_config();
            foreach ($configs as $c => $conf) {
                if ((!isset($conf['active']) || $conf['active'] === true) && isset($conf['show_in_display']) && $conf['show_in_display']  && $conf['enable']) {
                    $menus[$antrianIndex]['child'][] = [
                        'title' => str_replace('Antrian ', '', $conf['title_list']),
                        'icon' => 'circle-o text-' . ($conf['class']),
                        'href' => base_url("antrian/$c"),
                    ];
                }
            }
        } ?>

        <?php if ($antrianIndex) : ?>
            <?php foreach ($configs as $c => $conf) : ?>
                <?php if ((!isset($conf['active']) || $conf['active'] === true) && isset($conf['show_in_display']) && $conf['show_in_display']  && $conf['enable']) : ?>
                    <li class="nav-item nav-item-antrian d-none d-sm-inline-block">
                        <a href="<?php echo base_url("antrian/$c") ?>" class="nav-link px-2 text-truncate <?php echo $type == $c ? 'active' : '' ?>" style="max-width: 135px;"><?php echo str_replace('Antrian ', '', $conf['title_list']) ?></a>
                    </li>
                <?php endif ?>
            <?php endforeach ?>
        <?php endif*/ ?>
		</ul>
		<ul class="navbar-nav ms-auto">
			<?php if ($this->ion_auth->logged_in()): ?>
				<!-- Notifications Dropdown Menu -->
				<li class="nav-item dropdown" id="notification-container" style="display: none;">
					<a class="nav-link px-2" data-bs-toggle="dropdown" href="#">
						<i class="fa fa-bell"></i>
						<span class="badge text-bg-danger navbar-badge" id="notification-count">
							0
						</span>
					</a>
					<div class="dropdown-menu dropdown-menu-lg dropdown-menu-end" style="max-height: 400px; overflow-y: auto;" id="notification-dropdown">
						<span class="dropdown-item dropdown-header">
							<span id="notification-header">0 Notifikasi Disposisi</span>
						</span>
						<div class="dropdown-divider"></div>
						<div id="notification-list">
							<!-- Notifications will be loaded via AJAX -->
							<div class="dropdown-item text-center" id="loading-notifications">
								<i class="fa fa-spinner fa-spin"></i> Memuat notifikasi...
							</div>
						</div>
						<a href="<?php echo base_url('surat/masuk') ?>" class="dropdown-item dropdown-footer">Lihat semua surat</a>
					</div>
				</li>
			<?php endif ?>
			<li class="nav-item">
				<span class="nav-link px-2 chip realtime-clock text-xs d-none d-md-block"><?php echo format_date(date('Y-m-d H:i:s'), "EEEE, dd MMMM yyyy pukul HH:mm:ss") ?></span>
			</li>
			<?php if (isset($showParticles) && $showParticles): ?>
				<li class="nav-item">
					<span class="nav-link px-2"><?php $this->load->view('widgets/particletoggler') ?></span>
				</li>
			<?php endif ?>
			<li class="nav-item"> <a class="nav-link px-2 d-none d-md-block" href="#" data-lte-toggle="fullscreen"> <i data-lte-icon="maximize" class="fa fa-expand"></i> <i data-lte-icon="minimize" class="fa fa-compress" style="display: none;"></i> </a> </li>
			<?php if ($this->ion_auth->logged_in()): ?>
				<li class="nav-item dropdown user-menu">
					<a href="#" class="nav-link px-2 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="fa fa-user"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end" data-bs-popper="static"> <!--begin::User Image-->
						<li class="user-header user-panel d-flex justify-content-center align-items-center text-wrap flex-column">
							<img src="<?php echo $this->user->photoUrl ?>" class="img-circle elevation-4 m-2" alt="<?php echo $this->user->nama_lengkap ?>" style="object-fit: cover;">
							<a href="<?php echo base_url('kepegawaian/pegawai/profile') ?>" class="d-block fw-bold btn-modal" style="line-height: 15px;">
								<?php echo strtoupper($this->user->nama_lengkap) ?>
							</a>

							<span class="my-2 lh-1"><?php echo $this->user->jabatan ?></span>

							<?php $groups = $this->ion_auth->get_users_groups()->result() ?>
							<?php foreach ($groups as $group) : ?>
								<span class="badge badge-<?php echo ($group->id == $this->config->item('id_group_administrator') ? 'danger' : ($group->id == $this->config->item('id_group_pegawai') ? 'default' : 'primary')) ?>"><?php echo htmlspecialchars($group->name, ENT_QUOTES, 'UTF-8') ?></span>
							<?php endforeach ?>
						</li>

						<li class="nav-item user-footer p-1">
							<div class="pull-right">
								<a class="nav-link px-2 btn btn-sm btn-outline-warning" href="<?php echo base_url('/site/logout') ?>" data-method="post"><i class="fa fa-sign-out" aria-hidden="true"></i> Keluar</a>
							</div>
						</li>
					</ul>
				</li>
			<?php endif ?>
		</ul>
	</div>
</nav>

<script>
	$(document).ready(function() {
		// Function to load notifications via AJAX
		function loadNotifications() {
			$.ajax({
				url: '<?php echo base_url("notifikasi/get_disposition_notifications"); ?>',
				type: 'GET',
				dataType: 'json',
				beforeSend: function() {
					// Show loading indicator
					$('#loading-notifications').show();
					$('#notification-list').html('<div class="dropdown-item text-center" id="loading-notifications"><i class="fa fa-spinner fa-spin"></i> Memuat notifikasi...</div>');
				},
				success: function(response) {
					if (response.status === 'success') {
						var notifications = response.data;
						var html = '';

						if (notifications.length > 0) {
							// Show the notification container if it's hidden
							$('#notification-container').show();

							$.each(notifications, function(index, notification) {
								// Add different styling for read vs unread notifications
								var notificationClass = notification.is_read ? 'dropdown-item read-notification' : 'dropdown-item unread-notification';

								html += '<a href="<?php echo base_url("surat/masuk/view/") ?>' + notification.id + '" class="' + notificationClass + ' btn-modal" data-notification-id="' + notification.id + '">';
								html += '<div class="fw-bold text-truncate"><i class="fa fa-envelope mr-2"></i> ' + notification.letter_number + '</div>';
								html += '<div class="small text-muted mt-1 text-truncate"><small>' + notification.letter_subject + '</small></div>';
								html += '<div class="text-end small text-muted text-nowrap">' + notification.received_date_formatted + '</div>';
								html += '</a>';
								html += '<div class="dropdown-divider"></div>';
							});

							// Update notification count and header
							$('#notification-count').text(response.unread_count);
							$('#notification-header').text(response.unread_count + ' Notifikasi Disposisi');
						} else {
							// Hide the notification container if there are no notifications
							$('#notification-container').hide();

							html = '<a href="#" class="dropdown-item"><i class="fa fa-envelope mr-2"></i> Tidak ada notifikasi baru</a><div class="dropdown-divider"></div>';
							// Update notification count and header for zero notifications
							$('#notification-count').text(response.unread_count);
							$('#notification-header').text(response.unread_count + ' Notifikasi Disposisi');
						}

						$('#notification-list').html(html);
					} else {
						// Show the notification container if there were initial notifications
						<?php if (isset($this->user->unread_disposition_count) && $this->user->unread_disposition_count > 0): ?>
							$('#notification-container').show();
						<?php else: ?>
							$('#notification-container').hide();
						<?php endif; ?>

						$('#notification-list').html('<a href="#" class="dropdown-item">Gagal memuat notifikasi</a><div class="dropdown-divider"></div>');
					}

					// Hide loading indicator
					$('#loading-notifications').hide();
				},
				error: function(xhr, status, error) {
					console.error('Error loading notifications:', error);
					$('#notification-list').html('<a href="#" class="dropdown-item">Gagal memuat notifikasi</a><div class="dropdown-divider"></div>');
					$('#loading-notifications').hide();
				}
			});
		}

		// Load notifications on page load
		// loadNotifications();
		// setInterval(loadNotifications, 10 * 60 * 1000); // Refresh every 10 minutes

		// Handle click on individual notification to mark as read
		$(document).on('click', '.unread-notification', function(e) {
			var notificationId = $(this).data('notification-id');

			// Mark notification as read via AJAX
			$.ajax({
				url: '<?php echo base_url("notifikasi/mark_as_read"); ?>',
				type: 'POST',
				data: {
					notification_id: notificationId,
					'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
				},
				dataType: 'json',
				success: function(response) {
					if (response.status === 'success') {
						// Update the notification count in the header
						$('#notification-count').text(response.unread_count);
						$('#notification-header').text(response.unread_count + ' Notifikasi Disposisi');

						// Update the notification item to show it's read
						var $notificationItem = $('[data-notification-id="' + notificationId + '"]');
						$notificationItem.removeClass('unread-notification').addClass('read-notification');
					}
				},
				error: function(xhr, status, error) {
					console.error('Error marking notification as read:', error);
				}
			});
		});
	});

	// Add CSS for read/unread notification styling
	var style = document.createElement('style');
	style.type = 'text/css';
	style.innerHTML = `
    .unread-notification {
        font-weight: bold;
        background-color: #f8f9fa;
    }
    .read-notification {
        font-weight: normal;
        opacity: 0.7;
    }
`;
	document.getElementsByTagName('head')[0].appendChild(style);
</script>