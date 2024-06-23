<div class="card mb-4">
    <div class="card-header with-elements">
        <h5 class="card-header-title mr-2 m-0">Contacts</h5>

    </div>
    <div class="card-datatable table-responsive">
		<div class="col-lg-12" style="float:right;">
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
        <table class="table table-striped inner-datatable nowrap table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>First Name</th>                   
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Street</th>
                    <th>City</th>
                    <th>State</th>                    
                    <th>Zip Code</th>
                    <th>Phone</th>
                    <th>Tax Record Letter</th>
                    <th>Do Not Send</th>
                    <th>Do Not Blast</th>
                    <th>Active Buyer</th>
                    <th>Bad #</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($company['contacts'] as $contact) { ?>
                    <tr>
                        <td><?php echo @$offset += 1; ?></td>
                        <td><a href="<?= base_url("contact/view/" . $contact['contact_id']); ?>"><?php echo htmlspecialchars($contact['first_name']); ?></a></td>
                        <td><?php echo htmlspecialchars($contact['last_name']); ?></td>
                         <td><?php echo htmlspecialchars($contact['email']); ?></td>
                        <td><?php echo htmlspecialchars($contact['company_name']); ?></td>
                        <td><?php echo htmlspecialchars(@$contact['addresses'][0]->address); ?></td>
                        <td><?php echo htmlspecialchars(@$contact['addresses'][0]->city); ?></td>
                        <td><?php echo htmlspecialchars(@$contact['addresses'][0]->state); ?></td>
                        <td><?php echo htmlspecialchars(@$contact['addresses'][0]->zip_code); ?></td>
                        <td><?php echo htmlspecialchars(@$contact['phones'][0]->phone); ?></td>
                        <td><?= (@$contact['tax_record_sent_date'] != null) ? formatDate(@$contact['tax_record_sent_date']) : ''; ?></td>
                        <td><?= (@$contact['do_not_send'] == 1) ? 'Yes' : 'No'; ?></td>
                        <td><?= (@$contact['do_not_blast'] == 1) ? 'Yes' : 'No'; ?></td>
                        <td><?= (@$contact['active_buyer'] == 1) ? 'Yes' : 'No'; ?></td>
                        <td><?= (@$contact['bad_no'] == 1) ? 'Yes' : 'No'; ?></td>
                        <td><?= htmlspecialchars(@$contact['comment'] == 1); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
</div>
