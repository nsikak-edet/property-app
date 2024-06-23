
<div class="card mb-4">    
    <div class="card-body">        
        <div class="media">            
            <div class="media-body pt-2 ml-3">
                <a href="<?php echo base_url('contact/edit/' . $contact['contact_id']); ?>"
                                                                   class="btn btn-outline btn-primary btn-xs p--3 pr--5"><i
                                                                        class="fi fi-pencil pr-0 mr-0 ml-1"></i> Edit</a>
                                                                        <a href="<?php echo base_url('contact/remove/' . $contact['contact_id']); ?>" name="contact"
                                                                   class="btn btn-outline btn-outline-danger btn-xs delete-button-confirm p--3 pr--5 mb-"><i
                                                                                class="fi fi-pencil pr-0 mr-0 ml-1"></i> Delete</a><br>
                <h4 class="mb-2"><i class="ion ion-ios-contact mr-2 mt-3" ></i> <?= $pageTitle ?>                    
                
                </h4>
            </div>
        </div>
    </div>
    <hr class="border-light m-0">
    <div class="card-body">
		<div class="mb-2">
            <span class="text-muted ">First Name:</span>&nbsp;
            <?= htmlspecialchars(@$contact['first_name']); ?> 
        </div>        
        <div class="mb-2">
            <span class="text-muted ">Last Name:</span>&nbsp;
            <?= htmlspecialchars(@$contact['last_name']); ?> 
        </div>
		<div class="mb-2">
			<span class="text-muted ">Company:</span>&nbsp;
			<a href="<?= base_url("company/view/" . $contact['company_id']); ?>"><?= htmlspecialchars($contact['company_name']) ?></a>
		</div>
		<div class="mb-1">
			<span class="text-muted">Phone #:</span>&nbsp;
			<?php
			foreach ($contact['phones'] as $phone) {
				echo htmlspecialchars($phone->phone);
			}
			?>
		</div>
		<div class="mb-2">
			<span class="text-muted ">Email:</span>&nbsp;
			<?= htmlspecialchars(@$contact['email']); ?>
		</div>
		<?php foreach ($contact['addresses'] as $address): ?>
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
			<div class="mb-1">
				<span class="text-muted">Zip Code:</span>&nbsp;
				<?= htmlspecialchars($address->zip_code); ?>
			</div>
			<div class="mb-2">
				<span class="text-muted ">RPR:</span>&nbsp;
				<?= getPPRLink($address->address . ", " .$address->city. ", " . $address->state . " " . $address->zip_code); ?>
			</div>
		<?php endforeach; ?>
		<div class="mb-2">
			<span class="text-muted ">Lead Gen Type:</span>&nbsp;
			<?= htmlspecialchars(@$contact['lead_gen_type']); ?>
		</div>
		<div class="mb-2">
			<span class="text-muted ">Do Not Send:</span>&nbsp;
			<?= (@$contact['do_not_send'] == 0) ? "" : "Do Not Send"; ?>
		</div>
		<div class="mb-2">
			<span class="text-muted ">Do Not Blast:</span>&nbsp;
			<?= (@$contact['do_not_blast'] == 0) ? "" : "Do Not Blast"; ?>
		</div>
		<div class="mb-2">
			<span class="text-muted ">Active Buyer:</span>&nbsp;
			<?= (@$contact['active_buyer'] == 0) ? "" : "Active Buyer"; ?>
		</div>
		<div class="mb-2">
			<span class="text-muted">Bad #:</span>&nbsp;
			<?= (@$contact['bad_no'] == 0) ? "" : "Bad #"; ?>
		</div>
		<div class="mb-2">
			<span class="text-muted ">Last Update Date:</span>&nbsp;
			<?= ($contact['last_update'] != null) ? formatDate(@$contact['last_update']) : ''; ?>
		</div>
		<div class="mb-2">
			<span class="text-muted ">Last Dial Date:</span>&nbsp;
			<?= ($contact['last_dial'] != null) ? formatDate(@$contact['last_dial']) : ''; ?>
		</div>
		<div class="mb-2">
			<span class="text-muted ">Tax Record Letter Date:</span>&nbsp;
			<?= ($contact['tax_record_sent_date'] != null) ? formatDate(@$contact['tax_record_sent_date']) : ''; ?>
		</div>
		<br>
		<div class="mb-2">
			<span class="text-muted">Comment:</span>&nbsp;<br>
			<?= htmlspecialchars(@$contact['comment']); ?>
		</div>
    </div>
</div>

