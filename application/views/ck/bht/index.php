<div class="row">
	<div class="col-12">
		<div class="card leaves">
			<div class="card-header leaves d-flex align-items-center">
				<h5 class="m-0"><?php echo $title ?></h5>
			</div>
			<div class="card-body">
				<div class="callout callout-info m-4 mt-3">
					<h6>Informasi</h6>
					<ul>
						<li>Klik kolom TANGGAL RENCANA BHT untuk menentukan tanggal.</li>
						<li>Notifikasi pengingat tanggal BHT dapat dikirim manual menggunakan tombol KIRIM NOTIFIKASI.</li>
					</ul>
				</div>

				<div class="card card-filter" style="display: none;">
					<div class="card-body">
						<form id="filter-form" class="row g-3 align-items-center">
							<div class="col-auto">
								<label class="form-label">Tahun Putus:</label>
								<input type="text" class="form-control table-ghaib_datepicker" placeholder="Tahun Putus" value="<?php echo $this->uri->segment(4) ?: null ?>">
							</div>
							<div class="col-auto">
								<label class="form-label">Bulan Putus:</label>
								<input type="text" class="form-control datepicker_month" placeholder="Bulan Putus" value="">
							</div>
							<div class="col-auto">
								<label class="form-label">Tanggal Rencana BHT:</label>
								<input type="text" class="form-control datepicker_rencana_bht" placeholder="Tanggal Rencana BHT" value="">
							</div>
							<div class="col-auto">
								<label class="form-label">Tanggal BHT:</label>
								<input type="text" class="form-control datepicker_bht" placeholder="Tanggal BHT" value="">
							</div>
							<div class="col-auto">
								<label class="form-label">Status BHT:</label>
								<select class="form-select dropdown-bht-<?php echo time() ?>">
									<option value="">Pilih Status BHT</option>
									<option value="1">Sudah BHT</option>
									<option value="2">Belum BHT</option>
								</select>
							</div>
							<div class="col-auto">
								<label class="form-label">Status AC:</label>
								<select class="form-select dropdown-ac-<?php echo time() ?>">
									<option value="">Pilih Status AC</option>
									<option value="1">Sudah AC</option>
									<option value="2">Belum AC</option>
								</select>
							</div>
						</form>
					</div>
				</div>

				<div class="table-responsive">
					<table id="table-disposisi-<?php echo $this->uri->segment(4) ?>-<?php echo $this->uri->segment(5) ?>-<?php echo $this->uri->segment(6) ?>" class="display">
						<thead>
							<tr>
								<th>No.</th>
								<th>Nomor Perkara</th>
								<th>KM & PP</th>
								<th>Tanggal Putus</th>
								<th>Tanggal Rencana BHT</th>
								<th>Tanggal BHT</th>
								<th>Tanggal Akta Cerai</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		const theTime = '<?php echo time() ?>';

		// Initialize datepickers
		$('.table-ghaib_datepicker').datepicker({
			minViewMode: 'years',
			format: 'yyyy',
			autoclose: true,
			clearBtn: true
		});

		$('.datepicker_month').datepicker({
			minViewMode: 'months',
			format: 'yyyy-mm',
			autoclose: true,
			clearBtn: true
		});

		$('.datepicker_bht, .datepicker_rencana_bht').datepicker({
			format: 'yyyy-mm-dd',
			autoclose: true,
			clearBtn: true
		});

		// Function to apply filters
		function applyFilters() {
			$('#table-disposisi-<?php echo $this->uri->segment(4) ?>-<?php echo $this->uri->segment(5) ?>-<?php echo $this->uri->segment(6) ?>').DataTable().ajax.reload();
		}

		// Apply filters on each filter change
		$('.table-ghaib_datepicker, .datepicker_month, .datepicker_bht, .datepicker_rencana_bht, .dropdown-bht-<?php echo time() ?>, .dropdown-ac-<?php echo time() ?>').on('change', function() {
			applyFilters();
		});

		// Add clear functionality to dropdowns
		$('.dropdown-bht-<?php echo time() ?>, .dropdown-ac-<?php echo time() ?>').each(function() {
			const select = $(this);
			const dropdownWrapper = $('<div class="input-group dropdown-with-clear"></div>');
			select.wrap(dropdownWrapper);

			const clearBtn = $('<span class="input-group-text bg-light text-danger cursor-pointer clear-dropdown" title="Clear selection" style="cursor: pointer;">&times;</span>');
			select.after(clearBtn);

			clearBtn.on('click', function() {
				select.val('').trigger('change');
			});

			// Update clear button visibility based on selection
			function updateClearButton() {
				if (select.val() === '') {
					clearBtn.hide();
				} else {
					clearBtn.show();
				}
			}

			// Initial state
			updateClearButton();

			// Update when selection changes
			select.on('change', updateClearButton);
		});

		// For datepickers, also trigger on date selection
		$('.table-ghaib_datepicker, .datepicker_month, .datepicker_bht, .datepicker_rencana_bht').on('changeDate', function() {
			applyFilters();
		});

		const notifUrl = '<?php echo base_url('ck/bht/send_notif_rencana_bht?use_queue=false') ?>';

		// Initialize DataTable without the filter buttons in layout
		initDataTable("#table-disposisi-<?php echo $this->uri->segment(4) ?>-<?php echo $this->uri->segment(5) ?>-<?php echo $this->uri->segment(6) ?>", {
			ajax: {
				url: "<?php echo base_url("ck/bht/get_list") ?>",
				data: function(d) {
					d['selectedYear'] = $('.table-ghaib_datepicker').val();
					d['selectedMonth'] = $('.datepicker_month').val();
					d['selectedDateBHT'] = $('.datepicker_bht').val();
					d['selectedDateRencanaBHT'] = $('.datepicker_rencana_bht').val();
					d['selectedBht'] = $('.dropdown-bht-<?php echo time() ?>').val();
					d['selectedAc'] = $('.dropdown-ac-<?php echo time() ?>').val();
					d[localStorage.getItem('csrfName')] = localStorage.getItem('csrfToken');
				}
			},
			ajaxCellDatepicker: [{
				column: 4,
				url: '<?php echo site_url("ck/bht/update_value_disposisi/tanggal_rencana_bht") ?>',
				callback: '<?php echo site_url("ck/bht") ?>',
				editable: true
			}],
			rowCallback: function(row, data, index) {},
			scrollX: true, // Enable horizontal scrolling
			fixedColumns: {
				leftColumns: 2
			},
			columns: [{
					data: null,
					className: "dt-center",
					render: function(data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
				{
					data: "nomor_perkara",
					className: 'text-nowrap',
				},
				{
					data: "hakim_nama",
					className: "text-nowrap",
					render: function(data, type, row) {
						return data + '<br>' + row.panitera_nama.replace('Panitera Pengganti:', 'PP: ').trim();
					},
				},
				{
					data: "tanggal_putusan",
					className: "dt-center",
					render: function(data, type, row) {
						return data ? moment(data).format('Do MMMM YYYY') : '<span class="badge badge-danger">Belum</span>';
					},
				},
				{
					data: 'tanggal_rencana_bht',
					className: "dt-center",
					render: function(data, type, row) {
						if (!data && row.tanggal_bht && moment(row.tanggal_bht).isBefore(moment(), 'day')) {
							return '<span class="badge badge-success"><i class="fa fa-check" aria-hidden="true"></i></span>';
							// return moment(row.tanggal_bht).format('Do MMMM YYYY');
						}
						return data ? moment(data).format('Do MMMM YYYY') : '-';
					},
				},
				{
					data: 'tanggal_bht',
					className: "dt-center",
					render: function(data, type, row) {
						return data ? moment(data).format('Do MMMM YYYY') : '<span class="badge badge-danger">Belum</span>';
					},
				},
				{
					data: 'tgl_akta_cerai',
					className: "dt-center",
					render: function(data, type, row) {
						return data ? moment(data).format('Do MMMM YYYY') : '<span class="badge badge-danger">Belum</span>';
					},
				},
			],
			drawCallback: function() {
				// Show the filter card after DataTable is drawn
				$('.card-filter').show();
			},
			layout: {
				topEnd: {
					buttons: [{
						extend: 'customButton',
						text: '<span class="fa fa-whatsapp" aria-hidden="true"></span> Kirim Notifikasi',
						url: notifUrl,
						className: 'btn btn-outline-primary btn-progress btn-notification disabled',
						confirm: `Anda yakin akan mengirimkan notifikasi rencana BHT?`,
						title: 'Sedang mengirimkan notifikasi rencana BHT...'
					}]
				},
			}
		});
	});
</script>