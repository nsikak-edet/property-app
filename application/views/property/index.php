<div class="container-fluid flex-grow-1 container-p-y pb-0">
	<div class="row">
		<div class="d-flex col-xl-12 align-items-stretch">
			<!-- Content -->
			<div class="card w-100 mb-1">
				<div class="card-body">
					<?php echo form_open(base_url("property/"), array('method' => 'post', 'enctype' => 'multipart/form-data')) ?>
					<div class="form-group">
						<label class="form-label w-100">Property Upload File</label>
						<input type="file" name="file">
						<small class="form-text text-muted">Allowed files: .xls/xlsx only.</small>
						<span class="text-danger"><?php echo @$uploadError; ?></span>
						<a target='_blank' href="<?= base_url("/uploads/property-upload-temp.xlsx") ?>" class='mt-2'><i
								class='ios ion-ios-download '></i> download upload template</a>
					</div>
					<button type="submit" class="btn btn-default">
						<i class='ion ion-ios-cloud-upload'></i>
						Upload Properties
					</button>
					</form>
				</div>
			</div>
			<!-- /Content -->
		</div>
	</div>
</div>

<div class="container-fluid flex-grow-1 container-p-y mt-0">
	<div class="row">
		<div class="d-flex col-xl-12 align-items-stretch">
			<!-- Content -->
			<div class="card w-100 mb-1">
				<div class="card-body">
					<?php echo form_open(base_url("property/ajaxSearch/"), ['method' => 'post', 'id' => 'search-form']); ?>
					<div class="form-group col-12 mb-0 ">

						<div class="input-group mb-1">
							<input type="text" class="form-control"
								   value="<?php echo htmlspecialchars(@$search->name); ?>" name="name"
								   placeholder="to search multiple properties, seperate each tenant name with a comma e.g. prop a, prop b, prop c etc. ">
							<span class="input-group-append">
								<button class="btn btn-default advance-search" type="button" data-toggle="collapse"
										href="#accordion-1" aria-expanded="true">Advanced</button>
								<button class="btn btn-secondary search-button" type="submit">Search</button>
							</span>
						</div>
						<div id="accordion">
							<div class="card mb-2 bg-transparent">
								<div id="accordion-1" class="collapse <?php echo ($showAdvanceSearch) ? "show" : "" ?>"
									 data-parent="#accordion">
									<div class="card-body">

										<div class="form-row">
											<div class="form-group col-md-3">
												<label class="form-label">Store #</label>
												<input type="text" class="form-control"
													   value="<?php echo htmlspecialchars(@$search->store_no); ?>"
													   placeholder="Store #" name="store_no">
											</div>
											<div class="form-group col-md-9">
												<label class="form-label">Street Address</label>
												<input type="text" class="form-control" placeholder="Street Address"
													   value="<?php echo htmlspecialchars(@$search->street_address); ?>"
													   name="street_address">
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-4">
												<label class="form-label">City</label>
												<input type="text" class="form-control"
													   value="<?php echo htmlspecialchars(@$search->city); ?>"
													   placeholder="City" name="city">
											</div>
											<div class="form-group col-md-6 ">

												<label
													class="form-label">State: <?php echo (is_array(@$search->state)) ? implode(', ', @$search->state) : ''; ?></label>
												<select data-allow-clear="true" name="state[]" class="select2-states"
														style="width:100%"></select>
												<!--                                                <input type="text" class="form-control "  placeholder="for multiple, separate each sate by comma e.g state a, state b etc." value="" name="state">-->
											</div>
											<div class="form-group col-md-2">
												<label class="form-label">Zip Code</label>
												<input type="text" class="form-control" placeholder="Zip Code"
													   value="<?php echo htmlspecialchars(@$search->zip_code); ?>"
													   name="zip_code">
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-6">
												<label class="form-label">(Contacts & Companies) who own "x" or "x-y"
													amount of properties</label>
												<input type="text" class="form-control"
													   value="<?php echo htmlspecialchars(@$search->property_count); ?>"
													   placeholder="Enter count e.g 1, 2, 3... and range e.g. 1-2,1-5 etc"
													   name="property_count">
											</div>

											<div class="form-group col-md-3">
												<label class="form-label">Property
													Type: <?= htmlspecialchars(@$property['property_type']) ?></label>
												<select class="select2-property-types form-control "
														style="width: 100%;background-color: white!important;"
														name="property_type">
												</select>
												<div class="text-danger"><?= form_error('property_type') ?></div>
											</div>

											<div class="form-group col-md-3">
												<label class="form-label">Lead Gen Type</label>
												<select class="form-control"
														value="<?php echo htmlspecialchars(@$search->contact_name); ?>"
														placeholder="Lead Gen Type" name="lead_gen_type">
													<option value="">Select Type</option>
													<option
														value="<?= LeadGenOptions::MET ?>" <?= (@$search->lead_gen_type == LeadGenOptions::MET) ? "selected='selected'" : "" ?>>
														Met
													</option>
													<option
														value="<?= LeadGenOptions::HAVENT_MET ?>" <?= (@$search->lead_gen_type == LeadGenOptions::HAVENT_MET) ? "selected='selected'" : "" ?>>
														Haven't Met
													</option>
													<option
														value="<?= LeadGenOptions::MET_OR_HAVENT_MET ?>" <?= (@$search->lead_gen_type == LeadGenOptions::MET_OR_HAVENT_MET) ? "selected='selected'" : "" ?>>
														Met or Haven't Met
													</option>
													<option
														value="<?= LeadGenOptions::NOT_MET_OR_HAVENT_MET ?>" <?= (@$search->lead_gen_type == LeadGenOptions::NOT_MET_OR_HAVENT_MET) ? "selected='selected'" : "" ?>>
														Not a Met or Haven't Met
													</option>
												</select>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-4">
												<label class="form-label">Do Not Blast</label>
												<select class="form-control" placeholder="Do Not Blast"
														name="do_not_blast">
													<option value="">Select Type</option>
													<option value="<?= DoNotSendOptions::YES ?>">
														Blank
													</option>
													<option
														value="<?= DoNotSendOptions::NO ?>" <?= (@$search->do_not_blast == 1) ? "selected='selected'" : "" ?>>
														Do Not Blast
													</option>
												</select>
											</div>

											<div class="form-group col-md-4">
												<label class="form-label">Do Not Send</label>
												<select class="form-control" placeholder="Do Not Send"
														name="do_not_send">
													<option value="">Select Type</option>
													<option value="<?= DoNotSendOptions::YES ?>">
														Blank
													</option>
													<option
														value="<?= DoNotSendOptions::NO ?>" <?= (@$search->do_not_send == 1) ? "selected='selected'" : "" ?>>
														Do Not Send
													</option>
												</select>
											</div>

											<div class="form-group col-md-4">
												<label class="form-label">Bad #</label>
												<select class="form-control" placeholder="Bad #" name="bad_no">
													<option value="">Select Type</option>
													<option value="<?= DoNotSendOptions::YES ?>">
														Blank
													</option>
													<option
														value="<?= DoNotSendOptions::NO ?>" <?= (@$search->bad_no == 1) ? "selected='selected'" : "" ?>>
														Bad #
													</option>
												</select>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-3">
												<label class="form-label">Has Owner</label>
												<select class="form-control" placeholder="Has Owner" name="has_owner">
													<option value="">Select Option</option>
													<option
														value="<?= DoNotSendOptions::YES ?>" <?= (@$search->has_owner == 1) ? "selected='selected'" : "" ?>>
														Yes
													</option>
													<option value="<?= DoNotSendOptions::NO ?>">
														No
													</option>
												</select>
											</div>

											<div class="form-group col-md-3">
												<label class="form-label">Website: <span
														class="text-muted"><?php echo htmlspecialchars(@$search->owner_website); ?></span></label>
												<select data-allow-clear="true" name="owner_website"
														class="select2-field select2-field-website" style="width:100%">
													<option value="empty">Is Empty</option>
													<option value="not-empty">Not Empty</option>
												</select>
											</div>

											<div class="form-group col-md-3">
												<label class="form-label">Owner Phone: <span
														class="text-muted"><?php echo htmlspecialchars(@$search->owner_phone); ?></span></label>
												<select data-allow-clear="true" name="owner_phone"
														class="select2-field select2-field-phone" style="width:100%">
													<option value="empty">Is Empty</option>
													<option value="not-empty">Not Empty</option>
												</select>
											</div>

											<div class="form-group col-md-3">
												<label class="form-label">Owner Street Address: <span
														class="text-muted"><?php echo htmlspecialchars(@$search->owner_address); ?></span></label>
												<select data-allow-clear="true" name="owner_address"
														class="select2-field select2-field-city" style="width:100%">
													<option value="empty">Is Empty</option>
													<option value="not-empty">Not Empty</option>
												</select>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-3">
												<label class="form-label">Last Update Date</label>
												<input type="text" name="last_update"
													   class="form-control daterange-picker-2"
													   value="<?= (@$_GET['last_update'] != null) ? (@$_GET['last_update']) : ''; ?>"
													   placeholder="Last update date">
											</div>

											<div class="form-group col-md-3">
												<label class="form-label">Last Dial</label>
												<input type="text" name="last_dial"
													   class="form-control lastdial-daterange"
													   value="<?= (@$search->last_dial) ?>"
													   placeholder="Last update date"/>
											</div>

											<div class="form-group col-md-3">
												<label class="form-label">Tax Record Sent</label>
												<div class="input-group">
													<input type="text" class="form-control daterange-picker"
														   value="<?= (@$search->tax_record_sent_date != null) ? (@$search->tax_record_sent_date) : ''; ?>"
														   name="tax_record_sent_date"
														   placeholder="Tax Record Letter Sent Date">
												</div>
											</div>

											<div class="form-group col-md-3">
												<label class="form-label">Last Sold Date</label>
												<div class="input-group">
													<input type="text" class="form-control daterange-picker"
														   value="<?= (@$search->last_sold_date != null) ? (@$search->last_sold_date) : ''; ?>"
														   name="last_sold_date"
														   placeholder="Last Sold Date">
												</div>
											</div>
										</div>

										<div class="form-row">
											<div class="mt-2 col-lg-12">
												<button type="submit" class="btn btn-primary mt-3 search-button">
													Search
												</button>
												<a style="margin-left:5px;"
												   href="<?php echo base_url('property/reset_form') ?>"
												   class="btn btn-outline-secondary mt-3">Clear Options</a>
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
					<h5 class="card-header-title mr-2 m-0 badge badge-secondary p-1">Total Properties in
						Database(<?= tofloat($totalProperties) ?>)</h5>

					<div class="card-header-elements ml-md-auto">
						<a href="<?php echo base_url("property/add") ?>" class="btn btn-xs btn-outline-primary">
							<span class="ion ion-md-add"></span> Add Property</a>
					</div>
				</div>
				<div class="card-body">
					<a href="<?php echo base_url("property/export_properties") ?>"
					   class="btn btn-xs btn-outline-primary mb-4">
						<span class="ion ion-md-download"></span> Export</a>
					<table class="table nowrap card-table  search-result-datatable table-bordered"
						   data-ordering="false">
						<thead>
						<tr class="bg-lighter">
							<th>#</th>
							<th>Tenant Name</th>
							<th>Store #</th>
							<th>Property Street Address</th>
							<th>Property City</th>
							<th>Property State</th>
							<th>Zip Code</th>
							<th>Property Type</th>
							<th>Last Update Date</th>
							<th>Last Dial</th>
							<th>Tax Record Sent</th>
							<th>Lead Gen Type</th>
							<th>Do Not Blast</th>
							<th>Do Not Send</th>
							<th>Bad #</th>
							<th>Owner</th>
							<th>Owner Phone</th>
							<th>Owner Address</th>
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
						<button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
								data-toggle="dropdown" aria-expanded="false"><span class="fas fa-file-export"></span>
							Excel Export
						</button>
						<div class="dropdown-menu" style="">
							<a class="dropdown-item" href="<?php echo base_url("property/export_owners") ?>"><span
									class="fas fa-file-download"></span> Owners</a>
							<a class="dropdown-item"
							   href="<?php echo base_url("property/export_company_owners") ?>"><span
									class="fas fa-file-download"></span> Companies</a>
							<a class="dropdown-item"
							   href="<?php echo base_url("property/export_contact_owners") ?>"><span
									class="fas fa-file-download"></span> Contacts</a>
						</div>
					</div>

					<table class="table card-table nowrap property-owners-datatable table-bordered"
						   data-ordering="false">
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
	$(function () {

		$('.owners-datatable').dataTable({
			dom: 'Bfrtip',
			buttons: [{
				extend: 'excel',
				className: 'btn btn-primary fe-icon fe-excel ml-0'
			},],
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
				columns: [{
					data: "sn"
				},
					{
						data: "name"
					},
					{
						data: "store_number"
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
						data: "zip_code"
					},
					{
						data: "property_type"
					},
					{
						data: "last_update"
					},
					{
						data: "last_dial"
					},
					{
						data: "tax_record_sent"
					},
					{
						data: "lead_gen_type"
					},
					{
						data: "do_not_blast"
					},
					{
						data: "do_not_send"
					},
					{
						data: "bad_no"
					},
					{
						data: "owner"
					},
					{
						data: "phones"
					},
					{
						data: "owner_address"
					},
					{
						data: "action"
					}
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
					processResults: function (data) {
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
					processResults: function (data) {
						return {
							results: data
						};
					}
				}
			});

		//initialize table with previous cached search criteria
		<?php if (isset($searchCriteria)) : ?>
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
			columns: [{
				data: "sn"
			},
				{
					data: "name"
				},
				{
					data: "store_number"
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
					data: "zip_code"
				},
				{
					data: "property_type"
				},
				{
					data: "last_update"
				},
				{
					data: "last_dial"
				},
				{
					data: "tax_record_sent"
				},
				{
					data: "lead_gen_type"
				},
				{
					data: "do_not_blast"
				},
				{
					data: "do_not_send"
				},
				{
					data: "bad_no"
				},
				{
					data: "owner"
				},
				{
					data: "phones"
				},
				{
					data: "owner_address"
				},
				{
					data: "action"
				}
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

		$('a[data-toggle="accordion"]').on('shown.bs.tab', function (e) {
			$.fn.dataTable.tables({
				visible: true,
				api: true
			}).columns.adjust();
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
	$(function () {
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
	});
</script>
