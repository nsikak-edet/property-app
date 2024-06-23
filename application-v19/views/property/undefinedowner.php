<div class="container-fluid flex-grow-1 container-p-y pb-0">
	<div class="row mb-3">
		<div class="col-lg-12">
			<a href="javascript:history.go(-1)" class="pb-5 text-secondary"><i class="ion ion-ios-arrow-back mr-2" ></i> Back </a>
		</div>
	</div>
	<div class="row">
		<div class="d-flex col-xl-12 align-items-stretch">
			<!-- Content -->
			<div class="card w-100 mb-1">
				<div class="card-header">
					<h4>Undefined Owner</h4>
				</div>
				<div class="col-lg-12 mt-3" style="float:right;">
					<ul class="pagination pagination-sm mb-0" style="float:right">
						<?php if(@$hasPrevious): ?>
							<li class="page-item">
								<a class="page-link btn-outline-secondary"
								   href="<?= (isset($previousRecordLink)) ? $previousRecordLink : base_url('company/view/' . @$company['company_id'] . "?type=previous"); ?>">Previous</a>
							</li>
						<?php endif; ?>

						<?php if(@$hasNext): ?>
							<li class="page-item">
								<a class="page-link btn-outline-secondary ml-2" href="<?= (isset($nextRecordLink)) ? $nextRecordLink : base_url('company/view/' . @$company['company_id'] . "?type=next"); ?>">Next</a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
				<div class="card-body">

				</div>
			</div>
			<!-- /Content -->
		</div>
	</div>
</div>

<style type="text/css">
	.dataTables_scrollHeadInner {
		width: 100%;
	}

	div#DataTables_Table_0_wrapper {
		width: 100% !important;
	}

	/*div#DataTables_Table_0_wrapper {*/
	/*	width: 100% !important;*/
	/*}*/

	#DataTables_Table_0_filter, #DataTables_Table_1_filter {
		display: none;
	}

	/*.pagination{*/
	/*	display:none;*/
	/*}*/
</style>


<script type="text/javascript">
	$(function () {

		$('.owners-datatable').dataTable({
			dom: 'Bfrtip',
			buttons: [
				{extend: 'excel', className: 'btn btn-primary fe-icon fe-excel ml-0'},
			],
		});

		$('.search-button').on("click", function (e) {
			e.preventDefault();
			var formData = $('#search-form').serializeJSON();

			//initialize property table
			$('.search-result-datatable').dataTable({
				processing: true,
				serverSide: true,
				ajax: function (data, callback, settings) {
					formData.start = data.start;
					$.ajax({
						type: 'POST',
						url: "<?= base_url("property/ajaxSearch/1") ?>",
						data: formData,
						dataType: 'json',
						success: function (response) {
							callback({
								draw: data.draw,
								data: response.data,
								recordsTotal: response.recordsTotal,
								recordsFiltered: response.recordsFiltered
							});

							$('.delete-button-confirm').on('click', function (e) {
								e.preventDefault();


								var link = $(this).attr("href");
								var name = $(this).attr("name");
								bootbox.confirm("Are you sure you want to delete this " + name + "?", function (result) {
									if (result == true) {
										window.location.href = link;
									}
								})
							})
						},
						beforeSend: function () {
							$('.search-button').text('searching...');
							$('.search-button').prop('disabled', true);
						},
						complete: function () {
							$('.search-button').text('Search');
							$('.search-button').prop('disabled', false);
						},
					});
				},
				columns: [
					{data: "sn"},
					{data: "name"},
					{data: "address"},
					{data: "city"},
					{data: "state"},
					{data: "zip_code"},
					{data: "store_number"},
					{data: "owner"},
					{data: "type"},
					{data: "phones"},
					{data: "email"},
					{data: "owner_address"},
					{data: "owner_city"},
					{data: "owner_state"},
					{data: "owner_zip_code"},
					{data: "property_type"},
					{data: "tax_record_sent"},
					{data: "last_update"},
					{data: "action"}
				],
				pageLength: 100,
				paging: true,
				scrollY: 250,
				scrollX: true,
				buttons: [],
				bDestroy: true
			});

			//initialize owners table
			$('.property-owners-datatable').dataTable({
				processing: true,
				serverSide: true,
				ajax: function (data, callback, settings) {
					formData.start = data.start;
					$.ajax({
						type: 'POST',
						url: "<?= base_url("property/ajaxSearch/0") ?>",
						data: formData,
						dataType: 'json',
						success: function (response) {
							callback({
								draw: data.draw,
								data: response.data,
								recordsTotal: response.recordsTotal,
								recordsFiltered: response.recordsFiltered
							});
						},
						beforeSend: function () {
							$('.search-button').text('searching...');
							$('.search-button').prop('disabled', true);
						},
						complete: function () {
							$('.search-button').text('Search');
							$('.search-button').prop('disabled', false);
						},
					});
				},
				columns: [
					{data: "type"},
					{data: "owner"},
					{data: "company"},
					{data: "phones"},
					{data: "owner_address"},
					{data: "owner_city"},
					{data: "owner_state"},
					{data: "owner_zip_code"},
					{data: "email"},
				],
				pageLength: 100,
				paging: true,
				scrollY: 250,
				scrollX: true,
				buttons: [],
				"bDestroy": true
			});
			$.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
		});

		$('.select2-states')
			.wrap('<div class="position-relative"></div>')
			.select2({
				placeholder: 'Select States',
				multiple: true,
				ajax: {
					url: '<?php echo base_url("property/states") ?>',
					dataType: 'json',
					delay: 250,
					processResults: function (data) {
						return {
							results: data
						};
					}
				},
				tags: true
			});

		//initialize table with previous cached search criteria
		<?php if(isset($searchCriteria)): ?>
		$('.search-result-datatable').dataTable({
			processing: true,
			serverSide: true,
			ajax: function (data, callback, settings) {
				$.ajax({
					type: 'GET',
					url: "<?= base_url("property/ajaxSearch/1") ?>",
					dataType: 'json',
					success: function (response) {
						callback({
							draw: data.draw,
							data: response.data,
							recordsTotal: response.recordsTotal,
							recordsFiltered: response.recordsFiltered
						});

						$('.delete-button-confirm').on('click', function (e) {
							e.preventDefault();
							var link = $(this).attr("href");
							var name = $(this).attr("name");
							bootbox.confirm("Are you sure you want to delete this " + name + "?", function (result) {
								if (result == true) {
									window.location.href = link;
								}
							})
						})
					},
					beforeSend: function () {
						$('.search-button').text('searching...');
						$('.search-button').prop('disabled', true);
					},
					complete: function () {
						$('.search-button').text('Search');
						$('.search-button').prop('disabled', false);
					},
				});
			},
			columns: [
				{data: "sn"},
				{data: "name"},
				{data: "address"},
				{data: "city"},
				{data: "state"},
				{data: "zip_code"},
				{data: "store_number"},
				{data: "owner"},
				{data: "type"},
				{data: "phones"},
				{data: "email"},
				{data: "owner_address"},
				{data: "owner_city"},
				{data: "owner_state"},
				{data: "owner_zip_code"},
				{data: "property_type"},
				{data: "tax_record_sent"},
				{data: "last_update"},
				{data: "action"}
			],
			pageLength: 100,
			paging: true,
			scrollY: 250,
			scrollX: true,
			buttons: [],
			bDestroy: true
		});

		//initialize owners
		$('.property-owners-datatable').dataTable({
			processing: true,
			serverSide: true,
			ajax: function (data, callback, settings) {
				$.ajax({
					type: 'GET',
					url: "<?= base_url("property/ajaxSearch/0") ?>",
					dataType: 'json',
					success: function (response) {
						callback({
							draw: data.draw,
							data: response.data,
							recordsTotal: response.recordsTotal,
							recordsFiltered: response.recordsFiltered
						});
					},
					beforeSend: function () {
						$('.search-button').text('searching...');
						$('.search-button').prop('disabled', true);
					},
					complete: function () {
						$('.search-button').text('Search');
						$('.search-button').prop('disabled', false);
					},
				});
			},
			columns: [
				{data: "type"},
				{data: "owner"},
				{data: "company"},
				{data: "phones"},
				{data: "owner_address"},
				{data: "owner_city"},
				{data: "owner_state"},
				{data: "owner_zip_code"},
				{data: "email"},
//                    {data: "action"}
			],
			pageLength: 100,
			paging: true,
			scrollY: 250,
			scrollX: true,
			buttons: [],
			bDestroy: true
		});

		<?php endif; ?>

		$('a[data-toggle="accordion"]').on('shown.bs.tab', function (e) {
			$.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
		});

		$('#tabs').tabs();

	});
</script>

<script type="text/javascript">
	$(function () {
		var isRtl = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';

		//date range function
		function cb(start, end) {
			$('.daterange-picker').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		}

		var start = null;
		var end = null;

		$('.daterange-picker-2').daterangepicker({
			startDate: '<?= @$lastUpdateStartDate; ?>',
			endDate: '<?= @$lastUpdateEndDate; ?>',
			ranges: {
				'None ': [null, null],
				'No Date': [moment(), null],
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			},
			opens: (isRtl ? 'left' : 'right')
		}, cb);

		cb(start, end);
	});
</script>

