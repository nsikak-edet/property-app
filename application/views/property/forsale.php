<div class="container-fluid flex-grow-1 container-p-y mt-0">
	<div class="row">
		<div class="d-flex col-xl-12 align-items-stretch">
			<!-- Content -->
			<div class="card w-100 mb-1">
				<div class="card-body">
					<?php echo form_open(base_url("forsale/ajaxSearch/"), ['method' => 'post', 'id' => 'search-form']); ?>
					<div class="form-group col-12 mb-0 ">

						<div class="input-group mb-1">
							<input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->name); ?>" name="name" placeholder="to search multiple properties, seperate each tenant name with a comma e.g. prop a, prop b, prop c etc. ">
							<span class="input-group-append">
								<button class="btn btn-default advance-search" type="button" data-toggle="collapse" href="#accordion-1" aria-expanded="true">Advanced</button>
								<button class="btn btn-secondary search-button" type="submit">Search</button>
							</span>
						</div>
						<div id="accordion">
							<div class="card mb-2 bg-transparent">
								<div id="accordion-1" class="collapse <?php echo ($showAdvanceSearch) ? "show" : "" ?>" data-parent="#accordion">
									<div class="card-body">

										<div class="form-row">
											<div class="form-group col-md-6">
												<label class="form-label">Store #</label>
												<input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->store_no); ?>" placeholder="Store #" name="store_no">
											</div>
											<div class="form-group col-md-6">
												<label class="form-label">Street Address</label>
												<input type="text" class="form-control" placeholder="Street Address" value="<?php echo htmlspecialchars(@$search->street_address); ?>" name="street_address">
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-4">
												<label class="form-label">City</label>
												<input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->city); ?>" placeholder="City" name="city">
											</div>
											<div class="form-group col-md-6 ">

												<label class="form-label">State: <?php echo (is_array(@$search->state)) ? implode(', ', @$search->state) : ''; ?></label>
												<select data-allow-clear="true" name="state[]" class="select2-states" style="width:100%"></select>
												<!--                                                <input type="text" class="form-control "  placeholder="for multiple, separate each sate by comma e.g state a, state b etc." value="" name="state">-->
											</div>
											<div class="form-group col-md-2">
												<label class="form-label">Zip Code</label>
												<input type="text" class="form-control" placeholder="Zip Code" value="<?php echo htmlspecialchars(@$search->zip_code); ?>" name="zip_code">
											</div>
										</div>

										<div class="form-row">

											<div class="form-group col-md-4">
												<label class="form-label">Availability Status</label>
												<select class="form-control select2-availability-type" style="width:100%" placeholder="Availability Status" name="availability_status[]">
													<option value="blank">Blank</option>
													<option value="<?= AvailabilityStatus::OFF_MARKET ?>" <?= (@$search->availability_status == AvailabilityStatus::OFF_MARKET) ? "selected='selected'" : "" ?>>
														Off Market
													</option>
													<option value="<?= AvailabilityStatus::ON_MARKET ?>" <?= (@$search->availability_status == AvailabilityStatus::ON_MARKET) ? "selected='selected'" : "" ?>>
														On Market
													</option>
													<option value="<?= AvailabilityStatus::UNDER_LOI ?>" <?= (@$search->availability_status == AvailabilityStatus::UNDER_LOI) ? "selected='selected'" : "" ?>>
														Under LOI
													</option>
													<option value="<?= AvailabilityStatus::UNDER_CONTRACT ?>" <?= (@$search->availability_status == AvailabilityStatus::UNDER_CONTRACT) ? "selected='selected'" : "" ?>>
														Under Contract
													</option>
													<option value="<?= AvailabilityStatus::PIPELINE ?>" <?= (@$search->availability_status == AvailabilityStatus::PIPELINE) ? "selected='selected'" : "" ?>>
														Pipeline
													</option>
												</select>
											</div>

											<div class="form-group col-md-4">
												<label class="form-label">Availability Status Update Date</label>
												<input type="text" name="availability_update" class="form-control daterange-picker-2" value="<?= (@$_GET['availability_update'] != null) ? (@$_GET['availability_update']) : ''; ?>" placeholder="Availability Status Update Date ">
											</div>

											<div class="form-group col-md-4">
												<label class="form-label">Property Type: <?= htmlspecialchars(@$property['property_type']) ?></label>
												<select class="select2-property-types form-control " style="width: 100%;background-color: white!important;" name="property_type">
												</select>
												<div class="text-danger"><?= form_error('property_type') ?></div>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-6">
												<label class="form-label">Asking Price ($): <span class="text-muted"><span></label>
												<div class="input-group">
													<input type="text" class="form-control" placeholder="Min Price" name="asking_price[min_price]">
													<div class="input-group-append">
														<input type="text" class="form-control" placeholder="Max Price" name="asking_price[max_price]">
													</div>
												</div>
											</div>

											<div class="form-group col-md-6">
												<label class="form-label">Asking Cap Rate (%): <span class="text-muted"><span></label>
												<div class="input-group">
													<input type="text" class="form-control" placeholder="Min Rate" name="asking_rate[min_rate]">
													<div class="input-group-append">
														<input type="text" class="form-control" placeholder="Max Rate" name="asking_rate[max_rate]">
													</div>
												</div>
											</div>
										</div>

										<div class="form-row">
											<div class="mt-2 col-lg-12">
												<button type="submit" class="btn btn-primary mt-3 search-button">
													Search
												</button>
												<a style="margin-left:5px;" href="<?php echo base_url('forsale/reset_form') ?>" class="btn btn-outline-secondary mt-3">Clear Options</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
			<!-- /Content -->
		</div>
	</div>
</div>

<div class="container-fluid flex-grow-1 container-p-y">
	<div class="row">
		<div class="d-flex col-xl-12 align-items-stretch">
			<div class="card">
				<div class="card-header with-elements">
					<h4 class="mt-3">For Sale Properties</h4>
				</div>
				<div class="card-body">
					<a href="<?php echo base_url("forsale/export_properties") ?>" class="btn btn-xs btn-outline-primary mb-4">
						<span class="ion ion-md-download"></span> Export</a>
					<table class="table nowrap card-table  search-result-datatable table-bordered" >
						<thead>
							<tr class="bg-lighter">
								<th>#</th>
								<th>Tenant Name</th>
								<th>Property Street Address</th>
								<th>Property City</th>
								<th>Property State</th>
								<th>Annual Rent/NOI</th>
								<th>Asking Cap Rate</th>
								<th>Asking Price</th>
								<th>Minimum Lease Term Remaining</th>
								<th>Availability Status</th>
								<th>Availability Status Update Date</th>
								<th>Owner</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid flex-grow-1 container-p-y pt-0">
	<div class="row">
		<div class="d-flex col-xl-12 align-items-stretch">
			<div class="card" style="width:100% !important">
				<div class="card-header with-elements">
					<h5 class="card-header-title mr-2 m-0 p-1">Owners</h5>

				</div>
				<div class="card-body">
					<div class="btn-group" style="margin-left:-10px; padding-bottom:20px;">
						<button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"><span class="fas fa-file-export"></span> Excel Export</button>
						<div class="dropdown-menu" style="">
							<a class="dropdown-item" href="<?php echo base_url("forsale/export_owners") ?>"><span class="fas fa-file-download"></span> Owners</a>
							<a class="dropdown-item" href="<?php echo base_url("forsale/export_company_owners") ?>"><span class="fas fa-file-download"></span> Companies</a>
							<a class="dropdown-item" href="<?php echo base_url("forsale/export_contact_owners") ?>"><span class="fas fa-file-download"></span> Contacts</a>
						</div>
					</div>

					<table class="table card-table nowrap property-owners-datatable table-bordered" data-ordering="false">
						<thead>
							<tr class="bg-lighter">
								<th style="width:10%">Owner Type</th>
								<th style="width:20%">Owner</th>
								<th>Company</th>
								<th>Contact(s)</th>
								<th>Phone(s)</th>
								<th>Street Address</th>
								<th>City</th>
								<th>State</th>
								<th>Zip Code</th>
								<th>Email</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
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

	#DataTables_Table_0_filter,
	#DataTables_Table_1_filter {
		display: none;
	}

	/*.pagination{*/
	/*	display:none;*/
	/*}*/
</style>


<script type="text/javascript">
	$(function() {

		$('.owners-datatable').dataTable({
			dom: 'Bfrtip',
			buttons: [{
				extend: 'excel',
				className: 'btn btn-primary fe-icon fe-excel ml-0'
			}, ],
		});

		$('.search-button').on("click", function(e) {
			e.preventDefault();
			var formData = $('#search-form').serializeJSON();

			//initialize property table
			$('.search-result-datatable').dataTable({
				processing: true,
				serverSide: true,
				ajax: function(data, callback, settings) {
					formData.start = data.start;
					var sortData = {
						column: data.columns[data.order[0].column].data,
						dir: data.order[0].dir
					};
					formData.sort_data = sortData;

					console.log(formData);

					$.ajax({
						type: 'POST',
						url: "<?= base_url("forsale/ajaxSearch/1") ?>",
						data: formData,
						dataType: 'json',
						success: function(response) {
							callback({
								draw: data.draw,
								data: response.data,
								recordsTotal: response.recordsTotal,
								recordsFiltered: response.recordsFiltered
							});

							$('.delete-button-confirm').on('click', function(e) {
								e.preventDefault();


								var link = $(this).attr("href");
								var name = $(this).attr("name");
								bootbox.confirm("Are you sure you want to delete this " + name + "?", function(result) {
									if (result == true) {
										window.location.href = link;
									}
								})
							})
						},
						beforeSend: function() {
							$('.search-button').text('searching...');
							$('.search-button').prop('disabled', true);
						},
						complete: function() {
							$('.search-button').text('Search');
							$('.search-button').prop('disabled', false);
						},
					});
				},
				columns: [{
						data: "sn"
					},
					{
						data: "name"
					},
					{
						data: "address"
					},
					{
						data: "city"
					},
					{
						data: "state"
					},
					{
						data: "annual_rent"
					},
					{
						data: "asking_cap_rate"
					},
					{
						data: "asking_price"
					},
					{
						data: "lease_term_remaining"
					},
					{
						data: "availability_status"
					},
					{
						data: "availability_status_update_date"
					},
					{
						data: "owner"
					},
					{
						data: "action"
					}
				],
				pageLength: 100,
				ordering: true,
				paging: true,
				scrollY: 250,
				scrollX: true,
				buttons: [],
				bDestroy: true,
			});

			//initialize owners table
			$('.property-owners-datatable').dataTable({
				processing: true,
				serverSide: true,
				ajax: function(data, callback, settings) {
					formData.start = data.start;
					$.ajax({
						type: 'POST',
						url: "<?= base_url("forsale/ajaxSearch/0") ?>",
						data: formData,
						dataType: 'json',
						success: function(response) {
							callback({
								draw: data.draw,
								data: response.data,
								recordsTotal: response.recordsTotal,
								recordsFiltered: response.recordsFiltered
							});
						},
						beforeSend: function() {
							$('.search-button').text('searching...');
							$('.search-button').prop('disabled', true);
						},
						complete: function() {
							$('.search-button').text('Search');
							$('.search-button').prop('disabled', false);
						},
					});
				},
				columns: [{
						data: "type"
					},
					{
						data: "owner"
					},
					{
						data: "company"
					},
					{
						data: "contacts"
					},
					{
						data: "phones"
					},
					{
						data: "owner_address"
					},
					{
						data: "owner_city"
					},
					{
						data: "owner_state"
					},
					{
						data: "owner_zip_code"
					},
					{
						data: "email"
					},
				],
				pageLength: 100,
				paging: true,
				scrollY: 250,
				scrollX: true,
				buttons: [],
				"bDestroy": true
			});
			$.fn.dataTable.tables({
				visible: true,
				api: true
			}).columns.adjust();
		});

		$('.select2-availability-type')
			.wrap('<div class="position-relative"></div>')
			.select2({
				placeholder: 'Select',
				multiple: true,
				tags: true
			}).val('').trigger('change');

		$('.select2-states')
			.wrap('<div class="position-relative"></div>')
			.select2({
				placeholder: 'Select States',
				multiple: true,
				ajax: {
					url: '<?php echo base_url("property/states") ?>',
					dataType: 'json',
					delay: 250,
					processResults: function(data) {
						return {
							results: data
						};
					}
				},
				tags: true
			});

		$('.select2-property-types')
			.wrap('<div class="position-relative"></div>')
			.select2({
				placeholder: 'Property Type',
				multiple: false,
				tags: true,
				ajax: {
					url: '<?php echo base_url("property/property_types") ?>',
					dataType: 'json',
					delay: 250,
					processResults: function(data) {
						return {
							results: data
						};
					}
				}
			});

		//initialize table with previous cached search criteria
		<?php if (isset($searchCriteria)) : ?>
			var formData = $('#search-form').serializeJSON();
			$('.search-result-datatable').dataTable({
				processing: true,
				serverSide: true,
				ajax: function(data, callback, settings) {
					formData.start = data.start;
					var sortData = {
						column: data.columns[data.order[0].column].data,
						dir: data.order[0].dir
					};
					formData.sort_data = sortData;
					console.log(formData);

					$.ajax({
						type: 'GET',
						url: "<?= base_url("forsale/ajaxSearch/1") ?>",
						dataType: 'json',
						success: function(response) {
							callback({
								draw: data.draw,
								data: response.data,
								recordsTotal: response.recordsTotal,
								recordsFiltered: response.recordsFiltered
							});

							$('.delete-button-confirm').on('click', function(e) {
								e.preventDefault();
								var link = $(this).attr("href");
								var name = $(this).attr("name");
								bootbox.confirm("Are you sure you want to delete this " + name + "?", function(result) {
									if (result == true) {
										window.location.href = link;
									}
								})
							})
						},
						beforeSend: function() {
							$('.search-button').text('searching...');
							$('.search-button').prop('disabled', true);
						},
						complete: function() {
							$('.search-button').text('Search');
							$('.search-button').prop('disabled', false);
						},
					});
				},
				columns: [{
						data: "sn"
					},
					{
						data: "name"
					},
					{
						data: "address"
					},
					{
						data: "city"
					},
					{
						data: "state"
					},
					{
						data: "annual_rent"
					},
					{
						data: "asking_cap_rate"
					},
					{
						data: "asking_price"
					},
					{
						data: "lease_term_remaining"
					},
					{
						data: "availability_status"
					},
					{
						data: "availability_status_update_date"
					},
					{
						data: "owner"
					},
					{
						data: "action"
					}
				],
				pageLength: 100,
				paging: true,
				scrollY: 250,
				scrollX: true,
				// buttons: [],
				// bDestroy: true,
				// ordering: true,
				searchable: true
			});

			//initialize owners
			$('.property-owners-datatable').dataTable({
				processing: true,
				serverSide: true,
				ajax: function(data, callback, settings) {
					$.ajax({
						type: 'GET',
						url: "<?= base_url("forsale/ajaxSearch/0") ?>",
						dataType: 'json',
						success: function(response) {
							callback({
								draw: data.draw,
								data: response.data,
								recordsTotal: response.recordsTotal,
								recordsFiltered: response.recordsFiltered
							});
						},
						beforeSend: function() {
							$('.search-button').text('searching...');
							$('.search-button').prop('disabled', true);
						},
						complete: function() {
							$('.search-button').text('Search');
							$('.search-button').prop('disabled', false);
						},
					});
				},
				columns: [{
						data: "type"
					},
					{
						data: "owner"
					},
					{
						data: "company"
					},
					{
						data: "contacts"
					},
					{
						data: "phones"
					},
					{
						data: "owner_address"
					},
					{
						data: "owner_city"
					},
					{
						data: "owner_state"
					},
					{
						data: "owner_zip_code"
					},
					{
						data: "email"
					},
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

		$('a[data-toggle="accordion"]').on('shown.bs.tab', function(e) {
			$.fn.dataTable.tables({
				visible: true,
				api: true
			}).columns.adjust();
		});

		$('#tabs').tabs();

	});
</script>

<script type="text/javascript">
	$(function() {
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

		$('.lastdial-daterange').daterangepicker({
			startDate: '<?= @$lastUpdateStartDate; ?>',
			endDate: '<?= @$lastUpdateEndDate; ?>',
			ranges: {
				'None ': [null, null],
				'No Date': [moment(), null],
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'No Date & Older than 90 Days': [null, moment()],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			},
			opens: (isRtl ? 'left' : 'right')
		}, cb);

		$('.daterange-picker-3').daterangepicker({
			startDate: '<?= @$lastDialStartDate; ?>',
			endDate: '<?= @$lastDialEndDate; ?>',
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

<script type="text/javascript">
	$(function() {
		$('.search-datatable').dataTable({
			pageLength: 100,
			"scrollY": 350,
			"scrollX": true,
			searching: false,
			paging: false,
		});

		$('.select2-field')
			.wrap('<div class="position-relative"></div>')
			.select2({
				placeholder: 'Enter Value (select "empty" for empty records)',
				multiple: false,
				tags: true
			}).val('').trigger('change');

			$('.search-button').click();
	});
</script>