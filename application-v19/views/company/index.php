<div class="container-fluid flex-grow-1 container-p-y">
	<div class="row">
		<div class="d-flex col-xl-12 align-items-stretch">

			<!-- Content -->
			<div class="card w-100 mb-1">
				<div class="card-body">
					<?php echo form_open(base_url("company/"), array('method' => 'post', 'enctype' => 'multipart/form-data')) ?>
					<div class="form-group">
						<label class="form-label w-100">Company Upload File</label>
						<input type="file" name="file">
						<small class="form-text text-muted">Allowed files: .xls/xlsx only.</small>
						<span class="text-danger"><?php echo @$uploadError; ?></span>
						<a target='_blank' href="<?= base_url("/uploads/company-upload-temp.xlsx") ?>" class='mt-2'><i
								class='ios ion-ios-download '></i> download upload template</a>
					</div>
					<button type="submit" class="btn btn-default">
						<i class='ion ion-ios-cloud-upload'></i>
						Upload Companies
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
					<?php echo form_open(base_url("company/"), ['method' => 'get']); ?>
					<div class="form-group col-12 mb-0 ">
						<div class="input-group mb-1">
							<input type="text" class="form-control"
								   value="<?php echo htmlspecialchars(@$_GET['name']); ?>" name="name"
								   placeholder="search by company name">
							<input type="hidden" value="1" name="filter"/>
							<span class="input-group-append">
                                <button class="btn btn-default advance-search" type="button" data-toggle="collapse"
										href="#accordion-1" aria-expanded="true">Advanced</button>
                                <button class="btn btn-secondary" type="submit">Search</button>
                            </span>
						</div>
						<div id="accordion">
							<div class="card mb-2 bg-transparent">
								<div id="accordion-1" class="collapse" data-parent="#accordion">
									<div class="card-body">

										<div class="form-row">
											<div class="form-group col-md-6">
												<label class="form-label">Phone: <span class="text-muted"><?php echo htmlspecialchars(@$_GET['phone']); ?></span></label>
												<select data-allow-clear="true" name="phone" class="select2-field select2-field-phone" style="width:100%">
													<option value="empty">Is Empty</option>
													<option value="not-empty">Not Empty</option>
												</select>
											</div>
											<div class="form-group col-md-6">
												<label class="form-label">Street Address: <span class="text-muted"><?php echo htmlspecialchars(@$_GET['street_address']); ?></span></label>
												<select data-allow-clear="true" name="street_address" class="select2-field select2-field-city" style="width:100%">
													<option value="empty">Is Empty</option>
													<option value="not-empty">Not Empty</option>
												</select>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-3">
												<label class="form-label">City</label>
												<input type="text" class="form-control"
													   value="<?php echo htmlspecialchars(@$_GET['city']); ?>"
													   placeholder="City" name="city">
											</div>
											<div class="form-group col-md-3 ">
												<label class="form-label">State: <?php echo @@$_GET['state']; ?></label>
												<select data-allow-clear="true" name="state" class="select2-states"
														style="width:100%"></select>
											</div>
											<div class="form-group col-md-3">
												<label class="form-label">Zip Code</label>
												<input type="text" class="form-control" placeholder="Zip Code"
													   value="<?php echo htmlspecialchars(@$_GET['zip_code']); ?>"
													   name="zip_code">
											</div>

											<div class="form-group col-md-3">
												<label class="form-label">Last Update Date</label>
												<input type="text" name="last_update"
													   class="form-control daterange-picker-2"
													   value="<?= (@$_GET['last_update'] != null) ? (@$_GET['last_update']) : ''; ?>"
													   placeholder="Last update date">
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-6">
												<label class="form-label">Last Dial</label>
												<input type="text" name="last_dial"
													   class="form-control daterange-picker-3"
													   value="<?= (@$_GET['last_dial'] != null) ? (@$_GET['last_dial']) : ''; ?>"
													   placeholder="Last dial date">
											</div>
											<div class="form-group col-md-6">
												<label class="form-label">Tax Record Sent</label>
												<div class="input-group">
													<input type="text" class="form-control daterange-picker" value="<?= (@$_GET['tax_record_sent_date'] != null) ? (@$_GET['tax_record_sent_date']) : ''; ?>" name="tax_record_sent_date" placeholder="Tax Record Letter Sent Date">
												</div>
											</div>
										</div>


										<div class="form-row">
											<div class="mt-2 col-lg-12">
												<button type="submit" class="btn btn-primary mt-3">Search</button>
												<a style="margin-left:5px;" href="<?php echo base_url('company/') ?>"
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

			<!-- Content -->
			<div class="card w-100 mb-4">
				<div class="card-header with-elements">
					<h5 class="card-header-title mr-2 m-0"><?php echo $pageTitle; ?></h5>
					<div class="card-header-elements ml-md-auto">
						<a href="<?php echo base_url("company/add") ?>" class="btn btn-xs btn-outline-primary">
							<span class="ion ion-md-add"></span> Add Company</a>
					</div>
				</div>
				<div class="card-body" style=" padding-right:10px;">
					<p><?= number_format($totalRecords) ?> companies found</p>
					<table class="table m-0 card-table nowrap search-datatable mt-3" style='white-space:nowrap;'>
						<thead class="thead-light">
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Do Not Send</th>
							<th>Do Not Blast</th>
							<th style='width:25%'>Street</th>
							<th>City</th>
							<th>State</th>
							<th>Zip Code</th>
							<th>Tax Record Letter Sent Date</th>
							<th>Phone</th>
							<th>Last Update</th>
							<th>Last Dial</th>
							<th>Action</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($companies as $company) { ?>
							<tr>
								<td><?php echo @$offset += 1; ?></td>
								<td>
									<a href='<?= base_url("company/view/" . @$company['company_id']) ?>'><?php echo htmlspecialchars($company['name']); ?></a>
								</td>
								<td><?= (@$company['do_not_send'] == 0) ? "" : "Do Not Send"; ?></td>
								<td><?= (@$company['do_not_blast'] == 0) ? "" : "Do Not Blast"; ?></td>
								<td><?php echo htmlspecialchars(@$company['addresses'][0]->address); ?></td>
								<td><?php echo htmlspecialchars(@$company['addresses'][0]->city); ?></td>
								<td><?php echo htmlspecialchars(@$company['addresses'][0]->state); ?></td>
								<td><?php echo htmlspecialchars(@$company['addresses'][0]->zip_code); ?></td>
								<td><?= ($company['tax_record_sent_date'] != null) ? formatDate(@$company['tax_record_sent_date']) : ''; ?></td>
								<td><?php echo htmlspecialchars(@$company['phones'][0]->phone); ?></td>
								<td><?= ($company['last_update'] != null) ? formatDate(@$company['last_update']) : ''; ?></td>
								<td><?= ($company['last_dial'] != null) ? formatDate(@$company['last_dial']) : ''; ?></td>
								<td>
									<a href="<?php echo base_url('company/edit/' . $company['company_id']); ?>"
									   class="btn btn-outline btn-primary btn-xs p--3 pr--5"><i
											class="fi fi-pencil pr-0 mr-0 ml-1"></i> Edit</a>
									<a href="<?php echo base_url('company/remove/' . $company['company_id']); ?>"
									   name="company"
									   class="btn btn-outline-danger waves-effect delete-button-confirm waves-themed btn-xs p--3 pr--5 mr-1">
										<i
											class="fi fi-close pr-0 mr-0 ml-1"></i> Delete</a>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					<div class="mt-5"><?php echo $pagination->create_links() ?></div>

				</div>
			</div>
			<!-- /Content -->
		</div>
	</div>
</div>
<style type="text/css">
	.dataTables_info{display:none !important;}
</style>

<script type="text/javascript">
	$(function () {
		$('.search-datatable').dataTable({
			pageLength: 100,
			"scrollY": 350,
			"scrollX": true,
			searching: false,
			paging: false
		});

		$('.select2-states')
			.wrap('<div class="position-relative"></div>')
			.select2({
				placeholder: 'Select State',
				multiple: false,
				ajax: {
					url: '<?php echo base_url("company/states") ?>',
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

		$('.select2-field')
			.wrap('<div class="position-relative"></div>')
			.select2({
				placeholder: 'Enter Value (select "empty" for empty records)',
				multiple: false,
				tags: true
			}).val('').trigger('change');
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
				'None ': [null,null],
				'No Date': [moment(),null],
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
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
				'None ': [null,null],
				'No Date': [moment(),null],
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
