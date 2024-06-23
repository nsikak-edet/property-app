<div class="container-fluid flex-grow-1 container-p-y mt-0">
	<div class="row">
		<div class="d-flex col-xl-12 align-items-stretch">
			<!-- Content -->
			<div class="card w-100 mb-1">
				<div class="card-body">
					<?php echo form_open(base_url("activebuyer/ajaxSearch/"), ['method' => 'post', 'id' => 'search-form']); ?>
					<div class="form-group col-12 mb-0 ">

						<div class="input-group mb-1">
							<input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->name); ?>" name="name" placeholder="to search multiple, seperate each contact name with a comma e.g. contact a, contact b, contact c etc. ">
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
											<div class="form-group col-md-4">
												<label class="form-label">Landlord Responsibilities</label>
												<select class="form-control select2-landlord_responsibilities" style="width:100%" placeholder="Availability Status" name="landlord_reponsibilities[]">
													<option value="NNN">NNN</option>
													<option value="NN">NN</option>
													<option value="Gross">Gross</option>
													<option value="Ground">Ground</option>
													<option value="Leasehold">Leasehold</option>
												</select>
											</div>
											<div class="form-group col-md-4">
												<label class="form-label">Lease Term Remaining</label>
												<input type="text" class="form-control" placeholder="Lease Term Remaining" value="<?php echo htmlspecialchars(@$search->lease_term_remaining); ?>" name="lease_term_remaining">
											</div>
											<div class="form-group col-md-4 ">
												<label class="form-label">State: <?php echo (is_array(@$search->state)) ? implode(', ', @$search->state) : ''; ?></label>
												<select data-allow-clear="true" name="state[]" class="select2-states" style="width:100%"></select>
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
												</select>
											</div>

											<div class="form-group col-md-4">
												<label class="form-label">Buyer Status</label>
												<select class="form-control select2-buyer-status" style="width:100%" placeholder="Buyer Status" name="buyer_status[]">
													<option value="blank">Blank</option>
													<option value="<?= BuyerStatus::ACTIVE ?>" <?= (@$search->buyer_status == BuyerStatus::ACTIVE) ? "selected='selected'" : "" ?>>
														Active
													</option>
													<option value="<?= BuyerStatus::PIPELINE ?>" <?= (@$search->buyer_status == BuyerStatus::PIPELINE) ? "selected='selected'" : "" ?>>
														Pipeline
													</option>
												</select>
											</div>

											<div class="form-group col-md-4">
												<label class="form-label">Acquisition Criteria Update Date</label>
												<input type="text" name="acquisition_criteria_update" class="form-control daterange-picker-2" value="<?= (@$_GET['availability_update'] != null) ? (@$_GET['availability_update']) : ''; ?>" placeholder="Availability Status Update Date ">
											</div>

											<div class="form-group col-md-12">
												<label class="form-label">Tenant Name</label>
												<select class="form-control select2-tenants" style="width:100%" value="" placeholder="Tenant Name" name="tenant_names[]"></select>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-4">
												<label class="form-label">Asking Price ($): <span class="text-muted"><span></label>
												<div class="input-group">
													<input type="text" class="form-control" placeholder="Min Price" name="asking_price[min_price]">
													<div class="input-group-append">
														<input type="text" class="form-control" placeholder="Max Price" name="asking_price[max_price]">
													</div>
												</div>
											</div>

											<div class="form-group col-md-4">
												<label class="form-label">Asking Cap Rate (%): <span class="text-muted"><span></label>
												<div class="input-group">
													<input type="text" class="form-control" placeholder="Min Rate" name="asking_rate[min_rate]">
												</div>
											</div>

											<div class="form-group col-md-4">
												<label class="form-label">Property Type:</label>
												<select class="select2-property-types form-control " style="width: 100%;background-color: white!important;" name="property_types[]">

												</select>
											</div>

											<div class="form-group col-md-12">
												<label class="form-label">Sort By</label>
												<select class="form-control" style="width:100%" placeholder="Sort By" name="order_by">
													<option value="first_name">Contact Name</option>
													<option value="availability_status">Availability Status</option>
													<option value="min_asking_price">Minimum Asking Price</option>
													<option value="max_asking_price">Max Asking Price</option>
													<option value="min_asking_rate">Min Asking Cap Rate</option>
													<option value="max_asking_rate">Max Asking Cap Rate</option>
													<option value="lease_term_remaining">Minimum Lease Term Remaining</option>
													<option value="criteria_update_date">Acquisition Criteria Update Date</option>
												</select>
											</div>
										</div>

										<div class="form-row">
											<div class="mt-2 col-lg-12">
												<button type="submit" class="btn btn-primary mt-3 search-button">
													Search
												</button>
												<a style="margin-left:5px;" href="<?php echo base_url('activebuyer/reset_form') ?>" class="btn btn-outline-secondary mt-3">Clear Options</a>
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
					<h4 class="mt-3">Buyers</h4>
				</div>
				<div class="card-body">
					<a href="<?php echo base_url("activebuyer/export_active_buyers") ?>" class="btn btn-xs btn-outline-primary mb-4">
						<span class="ion ion-md-download"></span> Export</a>
					<table class="table nowrap card-table  search-result-datatable table-bordered">
						<thead>
							<tr class="bg-lighter">
								<th>#</th>
								<th>Contact Name</th>
								<th>Availability Status</th>
								<th>Property Type</th>
								<th>Min Asking Price</th>
								<th>Max Asking Price</th>
								<th>Min Asking Cap Rate</th>
								<th>Minimum Lease Term Remaining</th>
								<th>Landlord Responsibilities</th>
								<th>Tenant Name</th>
								<th>States</th>
								<th>Acquisition Criteria Update Date</th>
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

		$('.search-button').on("click", function(e) {
			e.preventDefault();
			var formData = $('#search-form').serializeJSON();

			//initialize property table
			$('.search-result-datatable').dataTable({
				processing: false,
				serverSide: true,
				ajax: function(data, callback, settings) {
					var sortData = {
						column: data.columns[data.order[0].column].data,
						dir: data.order[0].dir
					};
					formData.start = data.start;
					formData.sort_data = sortData;

					$.ajax({
						type: 'POST',
						url: "<?= base_url("activebuyer/ajaxSearch/") ?>",
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
						data: "owner"
					},
					{
						data: "availability_status"
					},
					{
						data: "property_type"
					},
					{
						data: "min_asking_price"
					},
					{
						data: "max_asking_price"
					},
					{
						data: "min_asking_rate"
					},
					{
						data: "lease_term_remaining"
					},
					{
						data: "landlord_responsibilities"
					},
					{
						data: "name"
					},
					{
						data: "state"
					},
					{
						data: "criteria_update_date"
					}
				],
				pageLength: 100,
				ordering: true,
				paging: true,
				scrollY: 250,
				scrollX: true,
				buttons: [],
				bDestroy: true
			});
		});

		$('.select2-availability-type')
			.wrap('<div class="position-relative"></div>')
			.select2({
				placeholder: 'Select',
				multiple: true,
				tags: true
			}).val('').trigger('change');

			$('.select2-buyer-status')
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
				multiple: true,
				tags: true,
				ajax: {
					url: '<?php echo base_url("property/property_types") ?>',
					dataType: 'json',
					delay: 250,
					processResults: function(data) {
						var option = {
							id: 'Retail Single-Tenant',
							text: 'Retail Single-Tenant'
						};
						data.push(option);

						var option = {
							id: 'Retail Multi-Tenant',
							text: 'Retail Multi-Tenant'
						};
						data.push(option);

						return {
							results: data
						};
					}
				}
			});
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

		$('.select2-landlord_responsibilities')
			.wrap('<div class="position-relative"></div>')
			.select2({
				placeholder: 'Select',
				multiple: true,
				tags: true
			}).val([]).trigger('change');


		$('.select2-tenants')
			.wrap('<div class="position-relative"></div>')
			.select2({
				placeholder: 'Select tenant',
				multiple: true,
				ajax: {
					url: '<?php echo base_url("property/tenants") ?>',
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

		$('.search-button').click();
	});
</script>