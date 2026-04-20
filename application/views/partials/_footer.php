<?php
$hasAssets = isset($assets) && is_array($assets);
$legacyFallback = !$hasAssets && empty($lightAssets);

$useDatepicker = ($hasAssets && !empty($assets['datepicker'])) || $legacyFallback;
$useViewer     = ($hasAssets && !empty($assets['viewer']))     || $legacyFallback;
$useDaterange  = ($hasAssets && !empty($assets['daterange']))  || $legacyFallback;
$useDataTables = ($hasAssets && !empty($assets['datatables'])) || $legacyFallback;
$useExport     = ($hasAssets && !empty($assets['export']))     || $legacyFallback; // jszip + pdfmake
$useKnob       = ($hasAssets && !empty($assets['knob']))       || $legacyFallback;
$useNprogress  = ($hasAssets && !empty($assets['nprogress']))  || $legacyFallback;
?>

<?php if ($useDatepicker): ?>
	<link rel="stylesheet" href="<?php echo asset_url('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
<?php endif; ?>

<link rel="stylesheet" href="<?php echo asset_url('assets/plugins/select2/select2.min.css') ?>">
<link rel="stylesheet" href="<?php echo asset_url('assets/plugins/select2/select2-bootstrap-5-theme.min.css') ?>">

<link rel="stylesheet" href="<?php echo asset_url('assets/css/waviy.css') ?>">

<?php if ($useNprogress): ?>
	<link rel="stylesheet" href="<?php echo asset_url('assets/plugins/nprogress/nprogress.css') ?>">
	<script src="<?php echo asset_url('assets/plugins/nprogress/nprogress.js') ?>" defer></script>
<?php endif; ?>

<?php if ($this->showFooter && is_local_ip()) : ?>
	<footer class="app-footer main-footer d-flex justify-content-between align-items-center">
		<span>
			<small>
				<a href="https://chandra.ct.ws" class="text-decoration-none" style="position: relative; z-index: 11;">CK &copy; https://chandra.ct.ws</a>
			</small>
		</span>
		<small>Load Time : <?php echo $this->benchmark->elapsed_time('code_start', 'code_end'); ?> Sec.&nbsp;|&nbsp;Memory Usage : <?php echo round(memory_get_peak_usage(false) / 1048576, 2) . "&nbsp;MB"; ?>/<?php echo ini_get('memory_limit'); ?></small>
		<div class="footer-right float-end d-none d-sm-flex align-items-center" style="position: relative; z-index: 11;"><span class="mx-2">Versi <?php echo $this->config->item('source_version') ?></span></div>
	</footer>
<?php endif ?>

<!-- Modal -->
<div class="modal fade" id="modal-input" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div id="modal-input-dialog" class="modal-dialog modal-dialog-scrollable modal-md"> <!-- modal-dialog-centered -->
		<div class="modal-content">
			<div class="modal-header">
				<div class="d-flex" style="margin: 0 auto;">
					<h5 class="modal-title" id="staticBackdropLabel"></h5>
				</div>
				<button type="button" class="btn-close ms-0 collapse" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body"></div>
			<big>
				<h1 id="counter" class="mb-3" style="color: red; text-align: center; margin-top: 0; display: none;"></h1>
			</big>
		</div>
	</div>
</div>

<!-- Toast -->
<div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3">
	<div id="globalToast" class="toast opacity-0" role="alert" aria-live="assertive" aria-atomic="true" style="--bs-bg-opacity: 0.6;">
		<div id="toastHeader" class="toast-header">
			<!-- <img src="..." class="rounded me-2" alt="..."> -->
			<strong id="toastTitle" class="me-auto"></strong>
			<!-- <small class="text-muted">11 mins ago</small> -->
		</div>
		<div id="toastBodyContainer" class="d-flex justify-content-center">
			<div id="toastBody" class="toast-body text-center w-100"></div>
		</div>
	</div>
</div>

<script src="<?php echo asset_url('assets/plugins/js-cookie/js.cookie.min.js') ?>"></script>

<?php if ($useDatepicker): ?>
	<!-- bootstrap datepicker -->
	<script src="<?php echo asset_url('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') ?>" defer></script>
<?php endif; ?>

<?php if ($legacyFallback): ?>
	<script src="<?php echo asset_url('assets/plugins/jquery-history/jquery.history.min.js') ?>" defer></script>
<?php endif; ?>

<script>
	window.ASSET_URL_PREFIX = "<?php echo rtrim(asset_url(''), '/') . '/'; ?>";
</script>
<script src="<?php echo asset_url('assets/js/main.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/color.js'); ?>"></script>

<script src="<?php echo asset_url('assets/plugins/popperjs/popper.min.js') ?>"></script>
<script src="<?php echo asset_url('assets/plugins/bootstrap/bootstrap.min.js') ?>"></script>
<script src="<?php echo asset_url('assets/plugins/adminlte4/dist/js/adminlte.js') ?>"></script>

<script src="<?php echo asset_url('assets/plugins/select2/select2.min.js') ?>"></script>

<?php if ($useKnob): ?>
	<script src="<?php echo asset_url('assets/plugins/jquery-knob/jquery.knob.min.js') ?>" defer></script>
<?php endif; ?>

<?php //if ($useDaterange): 
?>
<!-- Daterangepicker -->
<link rel="stylesheet" href="<?php echo asset_url('assets/plugins/daterange/daterangepicker.css') ?>">
<script src="<?php echo asset_url('assets/plugins/daterange/daterangepicker.js') ?>" defer></script>
<?php //endif; 
?>

<?php //if ($useDataTables): 
?>
<!-- DataTables -->
<link rel="stylesheet" href="<?php echo asset_url('assets/plugins/datatables/2.1.2/dataTables.dataTables.css') ?>">
<script src="<?php echo asset_url('assets/plugins/datatables/init.js') ?>" defer></script>
<script src="<?php echo asset_url('assets/plugins/datatables/2.1.2/dataTables.js') ?>" defer></script>

<!-- DataTables Plugins -->
<link rel="stylesheet" href="<?php echo asset_url('assets/plugins/datatables/dataTables.dataTables.plugins.css') ?>">
<script src="<?php echo asset_url('assets/plugins/datatables/dataTables.plugins.min.js') ?>" defer></script>
<?php //endif; 
?>

<?php //if ($useExport): 
?>
<script src="<?php echo asset_url('assets/plugins/jszip/jszip.min.js') ?>" defer></script>
<script src="<?php echo asset_url('assets/plugins/pdfmake/pdfmake.min.js') ?>" defer></script>
<script src="<?php echo asset_url('assets/plugins/pdfmake/vfs_fonts.js') ?>" defer></script>
<?php //endif; 
?>

<script type='text/javascript'>
	var languageUrl = "<?php echo asset_url('assets/plugins/datatables/Indonesian.json') ?>";
	var hideLoader = true;


	localStorage.setItem('csrfName', '<?php echo $this->security->get_csrf_token_name() ?>');
	localStorage.setItem('csrfToken', '<?php echo $this->security->get_csrf_hash() ?>');
	localStorage.setItem('isLoggedin', '<?php echo $this->ion_auth->logged_in() ? 1 : 0 ?>');

	<?php if ($this->session->flashdata('toast')): ?>
		<?php $toast = $this->session->flashdata('toast') ?>
		<?php $this->session->unset_userdata('toast') ?>
		document.addEventListener('DOMContentLoaded', function() {
			showToast(
				<?php echo json_encode(isset($toast['message']) ? $toast['message'] : '... message belum diset ...') ?>,
				<?php echo json_encode(isset($toast['title']) ? $toast['title'] : '') ?>,
				<?php echo json_encode(isset($toast['type']) ? $toast['type'] : 'bg-primary') ?>,
				<?php echo json_encode(isset($toast['autohide']) ? $toast['autohide'] : false) ?>,
				<?php echo json_encode(isset($toast['delay']) ? $toast['delay'] : 5000) ?>,
			);
		});
	<?php endif; ?>

	$(document).ready(function($) {
		// Watch for dynamically added .btn-notification elements or class changes
		let observerTriggered = false;
		let mutationCount = 0;
		const MAX_MUTATIONS_BEFORE_DISCONNECT = 50; // Safety limit

		const observer = new MutationObserver(function(mutations) {
			let hasNotificationButton = false;
			mutationCount += mutations.length;

			for (let i = 0; i < mutations.length; i++) {
				const mutation = mutations[i];

				if (mutation.type === 'childList') {
					// Check added nodes for .btn-notification
					for (let j = 0; j < mutation.addedNodes.length; j++) {
						const node = mutation.addedNodes[j];
						if (node.nodeType === 1) { // Element node
							if ($(node).hasClass('btn-notification') || $(node).find('.btn-notification').length > 0) {
								hasNotificationButton = true;
								break;
							}
						}
					}
				} else if (mutation.type === 'attributes') {
					// Check if the mutated element itself has the class or contains elements with the class
					if ($(mutation.target).hasClass('btn-notification') || $(mutation.target).find('.btn-notification').length > 0) {
						hasNotificationButton = true;
					}
				}

				if (hasNotificationButton) {
					break;
				}
			}

			if (!observerTriggered) {
				observerTriggered = true;

				if (typeof checkGateway === 'function') {
					// Clear existing content of #summary-info before calling checkGateway
					$('#summary-info').html('');
					checkGateway();

					// Disconnect observer after first call to prevent indefinite calls
					observer.disconnect();
				}
			} else if (mutationCount >= MAX_MUTATIONS_BEFORE_DISCONNECT) {
				// Safety disconnect: stop watching after too many mutations
				observer.disconnect();
			}
		});

		observer.observe(document.body, {
			childList: true,
			subtree: true,
			attributes: true, // Watch for attribute changes
			attributeFilter: ['class'] // Only watch for class attribute changes
		});

		intervalRefresh = <?php echo isset($refresh_interval) ? $refresh_interval : ($this->config->item('interval_refresh_list_antrian') ?: 10000) ?>;
		enableRefresh = '<?php echo $this->enableRefresh ?>' == 1;

		if (enableRefresh) {
			worker = new Worker('<?php echo asset_url('assets/js/worker.js') ?>');
			worker.postMessage(intervalRefresh);
			worker.onmessage = function() {
				if (!$('#modal-input.show').length) {
					callAjax({
						url: window.location.href,
						showBreadcrumb: true,
						showLoadingBar: false
					});
				}
			}
		}

		if ($.fn && $.fn.datepicker) {
			$('#datepicker').datepicker({
				format: "yyyy",
				viewMode: "years",
				minViewMode: "years",
				autoclose: true,
				clearBtn: true,
				todayBtn: 'linked',
				todayHighlight: true,
				// daysOfWeekDisabled: '06', //String, Array. Default: []. disable weekends: '06' or '0,6' or [0,6].
				// format: 'yyyy-mm-dd',
				// format: 'dd MM yyyy',
				// format: {
				//     toDisplay: function(date, format, language) {
				//         return date.toLocaleDateString("id-ID", {
				//             day: "2-digit",
				//             month: "long",
				//             year: "numeric",
				//         });
				//     },
				//     toValue: function(date, format, language) {
				//         return date
				//     }
				// },
				// startDate: '0d',
				// defaultViewDate: new Date(),
			}); //.datepicker("setDate", new Date());
		}
	});

	function callPrint(url) {
		busyShow();
		$.ajax({
			url: url,
			success: function(data) {
				busyHide();
				if (data.msg) {
					if (data.st == 1) {
						printTicket(data.print_data);
					}

					showToast(data.msg, '', data.st == 1 || data.st == undefined ? 'bg-primary' : 'bg-danger');
				}

				if (data.redirect) {
					callAjax({
						url: data.redirect,
						showBreadcrumb: false,
						showLoadingBar: false
					});
				} else {
					if (data.content && data.refresh == 1) {
						$('.container-main').html(data.content);
					}

					if (data.control) {
						$('.antrian-control').html(data.control);
					}
				}
			},
			error: function(jqXhr, textStatus, errorThrown) {
				busyHide();
				$('#modal-input').modal('hide');

				alert('Terjadi Kesalahan');
				// console.log(jqXhr);
				// console.log(textStatus);
				// console.log(errorThrown);
			}
		});
	}

	function checkGateway() {
		fetch('<?php echo base_url('site/check_gateway') ?>')
			.then(response => {
				if (!response.ok) {
					throw new Error('Network response was not ok ' + response.statusText);
				}
				return response.json();
			})
			.then(data => {
				if (data.status) {
					const result = JSON.parse(data.response);
					if (result.name) {
						if (!result.is_expired && !result.is_out_of_limit && result.status) {
							localStorage.setItem('btn-notification', result.status);
							$('.btn-notification').removeClass('disabled');
						}
						$('#summary-info').html(`<div class="alert alert-primary py-4" role="alert">
                            <h5>Pesan Terkirim</h5>
                            <div class="align-items-center d-flex">
                                <span class="me-2">Hari ini &nbsp; <span class="badge badge-success">${result.summary.today}</span></span> | &nbsp;
                                <span class="me-2">Kemarin &nbsp; <span class="badge badge-secondary">${result.summary.yesterday}</span></span> | &nbsp;

                                <span class="me-2">Minggu ini &nbsp; <span class="badge badge-success">${result.summary.cur_week}</span></span> | &nbsp;
                                <span class="me-2">Minggu Kemarin &nbsp; <span class="badge badge-secondary">${result.summary.prev_week}</span></span> | &nbsp;

                                <span class="me-2">Bulan ini &nbsp; <span class="badge badge-success">${result.summary.cur_month}</span></span> | &nbsp;
                                <span class="me-2">Bulan Kemarin &nbsp; <span class="badge badge-secondary">${result.summary.prev_month}</span></span> | &nbsp;

                                <span class="me-2">Semua &nbsp; <span class="badge badge-success">${result.summary.all}</span></span>
                            </div>
                        </div>`);
						$('.footer-right').html(`<div class="callout callout-${result.is_expired || result.is_out_of_limit || !result.status?'danger':'success'} d-flex align-items-center mb-0 ms-4 py-1" role="alert"><small>${result.name} (${result.number}) | ${result.status?'Aktif':'Tidak Aktif'} | Limit Pesan: ${new Intl.NumberFormat('id-ID').format(result.limit_message)} | Expired: ${(window.moment? moment(result.expires).format('dddd, Do MMMM YYYY h:mm') : new Date(result.expires).toLocaleString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }))}</small></div>`);
					} else {
						$('#summary-info').html('');
						$('.footer-right').html(`<div class="callout callout-danger d-flex align-items-center mb-0 ms-4 py-1" role="alert">${result.message}</div>`);
					}
				} else {
					$('#summary-info').html('');
					$('.footer-right').html(`<div class="callout callout-danger d-flex align-items-center mb-0 ms-4 py-1" role="alert">Sesi DialogWA tidak terhubung</div>`);
				}
			})
			.catch(error => {
				console.error('Fetch error:', error);
			});
	}
</script>