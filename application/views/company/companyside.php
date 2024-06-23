<div class="card mb-4">
	<div class="card-body">
		<div class="media">
			<div class="media-body pt-2 ml-3">
				<a href="<?php echo base_url('company/edit/' . $company['company_id']); ?>"
				   class="btn btn-outline btn-primary btn-xs p--3 pr--5"><i
						class="fi fi-pencil pr-0 mr-0 ml-1"></i> Edit</a>
				<a href="<?php echo base_url('company/remove/' . $company['company_id']); ?>" name="company"
				   class="btn btn-outline btn-outline-danger btn-xs p--3 pr--5 delete-button-confirm mb-"><i
						class="fi fi-pencil pr-0 mr-0 ml-1"></i> Delete</a><br>
				<h4 class="mb-2 mt-3"><i class="ion ion-md-business mr-2"></i> <?= $pageTitle ?><br>

				</h4>
				<div class="text-muted ">Company Details</div>

			</div>
		</div>
	</div>
	<hr class="border-light m-0">
	<div class="card-body">
		<div class="mb-2">
			<span class="text-muted">Name:</span>&nbsp;
			<?= htmlspecialchars(@$company['name']); ?>
		</div>
		<div class="mb-2">
			<span class="text-muted">Phone #:</span>&nbsp;
			<?php
			foreach ($company['phones'] as $phone) {
				echo htmlspecialchars("$phone->phone ");
			}
			?>
		</div>
		<?php foreach ($company['addresses'] as $address): ?>
			<div class="mb-2">
				<span class="text-muted">Street Address:</span>&nbsp;
				<?= htmlspecialchars($address->address); ?>
			</div>
			<div class="mb-2">
				<span class="text-muted">City:</span>&nbsp;
				<?= htmlspecialchars($address->city); ?>
			</div>
			<div class="mb-2">
				<span class="text-muted">State:</span>&nbsp;
				<?= htmlspecialchars($address->state); ?>
			</div>
			<div class="mb-2">
				<span class="text-muted">Zip Code:</span>&nbsp;
				<?= htmlspecialchars($address->zip_code); ?>
			</div>
		<?php endforeach; ?>

		<div class="mb-2">
			<span class="text-muted">Website:</span>
			<?= ($company['website'] != null) ? anchor($company['website'], '', array('target' => '_blank')) : ''; ?>
		</div>
		<div class="mb-2">
			<span class="text-muted">Do Not Send:</span>&nbsp;
			<?= (@$company['do_not_send'] == 0) ? "" : "Do Not Send"; ?>
		</div>
		<div class="mb-2">
			<span class="text-muted">Do Not Blast:</span>&nbsp;
			<?= (@$company['do_not_blast'] == 0) ? "" : "Do Not Blast"; ?>
		</div>
		<div class="mb-2">
			<span class="text-muted">Bad #:</span>&nbsp;
			<?= (@$company['bad_no'] == 0) ? "" : "Bad #"; ?>
		</div>
		<div class="mb-2">
			<span class="text-muted">Last Update Date:</span>
			<?= ($company['last_update'] != null) ? formatDate(@$company['last_update']) : ''; ?>
		</div>
		<div class="mb-2">
			<span class="text-muted">Last Dial Date:</span>
			<?= ($company['last_dial'] != null) ? formatDate(@$company['last_dial']) : ''; ?>
		</div>
		<div class="mb-2">
			<span class="text-muted">Tax Record Letter:</span>&nbsp;
			<?= ($company['tax_record_sent_date'] != null) ? formatDate(@$company['tax_record_sent_date']) : ''; ?>
		</div><br>
		<div class="mb-2">
			<span class="text-muted">Comment:</span>&nbsp;<br>
			<?= htmlspecialchars(@$company['comment']); ?>
		</div>

	</div>
</div>

