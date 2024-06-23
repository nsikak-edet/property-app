<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">

            <!-- Content -->
            <div class="card w-100 mb-1">
                <div class="card-body">
                    <?php echo form_open(base_url("contact/"), array('method' => 'post', 'enctype' => 'multipart/form-data')) ?>
                    <div class="form-group">
                        <label class="form-label w-100">Contact Upload File</label>
                        <input type="file" name="file">
                        <small class="form-text text-muted">Allowed files: .xls/xlsx only.</small>
                        <a target='_blank' href="<?= base_url("/uploads/contact-upload-temp.xlsx") ?>" class='mt-2'><i class='ios ion-ios-download '></i> download upload template</a>
                        <span class="text-danger"><?php echo @$uploadError; ?></span>
                    </div>
                    <button type="submit" class="btn btn-default">
                        <i class='ion ion-ios-cloud-upload'></i>
                        Upload Contacts</button>
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
                    <?php echo form_open(base_url("contact/"), ['method' => 'get']); ?>
                    <div class="form-group col-12 mb-0 ">
                        <div class="input-group mb-1">
                            <div class="input-group col-lg-12 ml-0 pl-0">
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$_GET['first_name']); ?>" placeholder="First Name" name="first_name">
                                <span class="input-group-append">
                                    <input type="text" class="form-control ml-1" value="<?php echo htmlspecialchars(@$_GET['last_name']); ?>" placeholder="Last Name" name="last_name">
                                </span>
                                <button class="btn btn-default advance-search ml-2" type="button" data-toggle="collapse" href="#accordion-1" aria-expanded="true">Advanced</button>
                                <button class="btn btn-secondary ml-1" type="submit">Search</button>
                            </div>
                            <input type="hidden" value="1" name="filter" />                            
                        </div>
                        <div id="accordion">
                            <div class="card mb-2 bg-transparent">
                                <div id="accordion-1" class="collapse" data-parent="#accordion" >
                                    <div class="card-body">

                                        <div class="form-row">
                                            <div class="form-group col-md-3">
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

											<div class="form-group col-md-3">
												<label class="form-label">Last Update Date</label>
												<input type="text" name="last_update"
													   class="form-control daterange-picker-2"
													   value="<?= (@$_GET['last_update'] != null) ? (@$_GET['last_update']) : ''; ?>"
													   placeholder="Last update date">
											</div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label class="form-label">City</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$_GET['city']); ?>" placeholder="City" name="city">
                                            </div>
                                            <div class="form-group col-md-6 ">
                                                <label class="form-label">State: <?php echo @$_GET['state']; ?></label>
                                                <select data-allow-clear="true" name="state" class="select2-states" style="width:100%"></select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="form-label">Zip Code</label>
                                                <input type="text" class="form-control" placeholder="Zip Code" value="<?php echo htmlspecialchars(@$_GET['zip_code']); ?>" name="zip_code">
                                            </div>
                                        </div>

                                        <div class="form-row">                                             
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Email</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$_GET['email']); ?>" placeholder="Email" name="email">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Company</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$_GET['company']); ?>" placeholder="Enter company name" name="company" >
                                            </div>

											<div class="form-group col-md-3">
												<label class="form-label">Tax Record Sent</label>
												<div class="input-group">
													<input type="text" class="form-control daterange-picker" value="<?= (@$_GET['tax_record_sent_date'] != null) ? (@$_GET['tax_record_sent_date']) : ''; ?>" name="tax_record_sent_date" placeholder="Tax Record Letter Sent Date">
												</div>
											</div>

											<div class="form-group col-md-3">
												<label class="form-label">Last Dial</label>
												<input type="text" name="last_dial"
													   class="form-control daterange-picker-3"
													   value="<?= (@$_GET['last_dial'] != null) ? (@$_GET['last_dial']) : ''; ?>"
													   placeholder="Last dial date">

											</div>
										</div>

                                        <div class="form-row">
                                            <div class="mt-2 col-lg-12">
                                                <button type="submit" class="btn btn-primary mt-3">Search</button>
                                                <a style="margin-left:5px;" href="<?php echo base_url('contact/') ?>" class="btn btn-outline-secondary mt-3">Clear Options</a>
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
                        <a href="<?php echo base_url("contact/add") ?>" class="btn btn-xs btn-outline-primary">
                            <span class="ion ion-md-add"></span> Add Contact</a>
                    </div>
                </div>
                <div class="card-body table-responsive" style="overflow-x: scroll; padding-right:10px;">
					<p><?= number_format($totalRecords) ?> contacts found</p>
                    <table class="table m-0 mt-3 nowrap card-table search-datatable" style='white-space:nowrap'>
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>First Name</th>                               
                                <th>Last Name</th>
								<th>Tax Record Letter Sent Date</th>
								<th>Last Update Date</th>
								<th>Last Dial</th>
								<th>Do Not Send</th>
								<th>Do Not Blast</th>
                                <th>Company</th>
                                <th>Lead Gen Type</th>
                                <th style='width:25%'>Street</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Zip Code</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact) { ?>
                                <tr>
                                    <td><?php echo @$offset += 1; ?></td>
                                    <td><a href="<?= base_url("contact/view/" . $contact['contact_id']) ?>"><?php echo htmlspecialchars($contact['first_name']); ?></a></td>                                    
                                    <td><?php echo htmlspecialchars($contact['last_name']); ?></td>                                    
                                    <td><?= ($contact['tax_record_sent_date'] != null) ? formatDate(@$contact['tax_record_sent_date']) : ''; ?></td>
                                    <td><?= ($contact['last_update'] != null) ? formatDate(@$contact['last_update']) : ''; ?></td>
                                    <td><?= ($contact['last_dial'] != null) ? formatDate(@$contact['last_dial']) : ''; ?></td>
                                    <td><?= (@$contact['do_not_send'] == 0) ? "" : "Do Not Send"; ?></td>
                                    <td><?= (@$contact['do_not_blast'] == 0) ? "" : "Do Not Blast"; ?></td>
                                    <td><a href="<?= base_url("company/view/" . $contact['company_id']) ?>"><?= htmlspecialchars($contact['company_name']); ?></a></td>
                                    <td><?php echo htmlspecialchars($contact['lead_gen_type']); ?></td>
                                    <td><?php echo htmlspecialchars(@$contact['addresses'][0]->address); ?></td>
                                    <td><?php echo htmlspecialchars(@$contact['addresses'][0]->city); ?></td>
                                    <td><?php echo htmlspecialchars(@$contact['addresses'][0]->state); ?></td>
                                    <td><?php echo htmlspecialchars(@$contact['addresses'][0]->zip_code); ?></td>
                                    <td><?php echo htmlspecialchars(@$contact['phones'][0]->phone); ?></td>
                                    <td><?php echo htmlspecialchars($contact['email']); ?></td>

                                    <td>
                                        <a href="<?php echo base_url('contact/edit/' . $contact['contact_id']); ?>"
                                           class="btn btn-outline btn-primary btn-xs p--3 pr--5"><i
                                                class="fi fi-pencil pr-0 mr-0 ml-1"></i> Edit</a>
                                        <a href="<?php echo base_url('contact/remove/' . $contact['contact_id']); ?>" name="contact"
                                           class="btn btn-outline-danger waves-effect delete-button-confirm waves-themed btn-xs p--3 pr--5 mr-1"> <i
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
            paging: false,
        });

        $('.select2-states')
                .wrap('<div class="position-relative"></div>')
                .select2({
                    placeholder: 'Select State',
                    multiple: false,
                    ajax: {
                        url: '<?php echo base_url("contact/states") ?>',
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

