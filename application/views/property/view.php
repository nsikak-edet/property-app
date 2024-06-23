<div class="container-fluid flex-grow-1 container-p-y">
	<div class="row mb-3">
		<div class="col-lg-10">
			<a href="javascript:history.go(-1)" class="pb-5 text-secondary"><i class="ion ion-ios-arrow-back mr-2"></i>
				Back </a>
		</div>


	</div>
	<div class="row">
		<div class="d-flex col-xl-12 align-items-stretch">

			<!-- Content -->
			<div class="card w-100 mb-4 ">
				<div class="card-header with-elements">
					<h5 class="card-header-title m-0 col-lg-10">
						<a href="<?php echo base_url('property/edit/' . $property['property_id']); ?>"
						   class="btn btn-outline btn-primary btn-xs p--3 pr--5"><i
								class="fi fi-pencil pr-0 mr-0 ml-1"></i> Edit</a>
						<a href="<?php echo base_url('property/remove/' . $property['property_id']); ?>" name="property"
						   class="btn btn-outline btn-outline-danger btn-xs delete-button-confirm p--3 pr--5 mb-"><i
								class="fi fi-pencil pr-0 mr-0 ml-1"></i> Delete</a>
					</h5>
				</div>

				<div class="card-body col-lg-12">
					<div class="col-lg-12" style="float:right;">
						<?php if (@$hideNext == false) : ?>
							<ul class="pagination pagination-sm mb-0" style="float:right">
								<?php if (@$hasPrevious) : ?>
									<li class="page-item">
										<a class="page-link btn-outline-secondary"
										   href="<?php echo base_url('property/view/' . $property['property_id']); ?>?type=previous">Previous</a>
									</li>
								<?php endif; ?>

								<?php if (@$hasNext) : ?>
									<li class="page-item">
										<a class="page-link btn-outline-secondary ml-2"
										   href="<?php echo base_url('property/view/' . $property['property_id']); ?>?type=next">Next</a>
									</li>
								<?php endif; ?>
							</ul>
						<?php endif; ?>
					</div>

					<p>
					<h3>Property</h3>
					<strong>Tenant Name: </strong> <?= htmlspecialchars($property['property_name']) ?><br>
					<strong>Property Type: </strong> <?= htmlspecialchars($property['property_type']) ?><br>
					<strong>Store #: </strong> <?= htmlspecialchars($property['store_number']) ?><br>
					<strong>Street Address: </strong> <?= htmlspecialchars($property['address']) ?><br>
					<strong>City: </strong> <?= htmlspecialchars($property['city']) ?><br>
					<strong>State: </strong> <?= htmlspecialchars($property['state']) ?><br>
					<strong>Zip Code: </strong> <?= htmlspecialchars($property['zip_code']) ?><br>
					<strong>RPR: </strong> <?= getPPRLink($property['address'] . ", " . $property['city'] . ", " . $property['state'] . " " . $property['zip_code']); ?>
					<br>
					<strong>Google
						Map: </strong> <?= (strlen($property['google_map_link']) > 2) ? "<a href='" . prep_url(trim($property['google_map_link'])) . "' target='_blank'>" . htmlspecialchars($property['google_map_link']) . "</a>" : '' ?>
					<br>
					<br>

					<strong>Availability Status: </strong> <?= htmlspecialchars($property['availability_status']); ?>
					<br>
					<strong>Availability Status Update
						Date: </strong> <?= ($property['availability_update_date'] != null) ? formatDate(@$property['availability_update_date']) : ''; ?>
					<br>
					<strong>Lease Type: </strong> <?= htmlspecialchars($property['lease_type']); ?><br>
					<strong>Annual Rent/NOI: </strong> <?= moneyFormat($property['annual_rent'], "USD"); ?><br>
					<strong>Asking Cap Rate: </strong> <?= htmlspecialchars($property['asking_cap_rate'] . "%"); ?><br>
					<strong>Asking Price: </strong> <?= "$" . number_format($property['asking_price']); ?><br>
					<strong>Lease Commencement
						Date: </strong> <?= ($property['lease_commencement_date'] != null) ? formatDate(@$property['lease_commencement_date']) : ''; ?>
					<br>
					<strong>Lease Expiration
						Date: </strong> <?= ($property['lease_expiration_date'] != null) ? formatDate(@$property['lease_expiration_date']) : ''; ?>
					<br>
					<strong>Lease Term Remaining: </strong> <?= ($property['lease_term_remaining']); ?><br>
					<strong>Building Size: </strong> <?= number_format($property['building_size'], 0); ?><br>
					<strong>Land Size: </strong> <?= number_format($property['land_size'], 0); ?><br>
					<strong>Comment: </strong> <?= htmlspecialchars($property['comments']); ?><br>
					<strong>Property
						Link: </strong> <?= (strlen($property['property_link']) > 2) ? "<a href='" . prep_url(trim($property['property_link'])) . "' target='_blank'>" . htmlspecialchars($property['property_link']) . "</a>" : '' ?>
					<br><br><br>

					<legend><h3>Property History</h3></legend>
					<?php echo form_open(base_url("property/add_history/"), array('method' => 'post')) ?>
					<div id="company-address-container" class="clone-data">
						<div class="company-address-row clone-div" data-index="0">
							<div class="form-row">
								<div class="form-group col-md-3">
									<label class="form-label">Last Sold Date</label>
									<input type="text" name="last_sold_date" placeholder="Last Sold Date"
										   class="form-control datepicker-base">
									<div class="text-danger"></div>
								</div>
								<div class="form-group col-md-3">
									<label class="form-label">Last Sold Price</label>
									<input type="hidden" name="property_id" value="<?= $property['property_id'] ?>">
									<input type="text" name="last_sold_price" placeholder="Last Sold Price"
										   class="form-control">
									<div class="text-danger"></div>
								</div>
							</div>
						</div>
					</div>
					<button type="submit" class="btn btn-sm btn-outline-dark mb-4" id="add-company-address"> + Add
						History
					</button>
					<?php echo form_close(); ?>
					</fieldset>

					<table class="table mt-0" style="width:50%">
						<thead>
						<tr>
							<th>Last Sold Price</th>
							<th>Last Sold Date</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						<?php foreach($propertyHistories as $historyData): ?>
						<tr>
							<td><?= "$" . number_format($historyData['last_sold_price']); ?></td>
							<td><?= ($historyData['last_sold_date'] != null) ? formatDate(@$historyData['last_sold_date']) : ''; ?></td>
							<td> <a class="btn btn-outline-danger btn-sm" href="<?= base_url("property/remove_history/") . $historyData['property_history_id'] ?>">Delete</a></td>
						</tr>
						<?php endforeach; ?>
						</tbody>
					</table>

					<br>
					<strong>Tax Record Sent: </strong> <?= ($property['tax_record_sent_date'] != null) ? formatDate(@$property['tax_record_sent_date']) : ''; ?><br>
					<strong>Last Update Date: </strong> <?= ($property['last_update'] != null) ? formatDate(@$property['last_update']) : ''; ?>
					<br>

					<h3 class="mt-5">Owner</h3>
					<strong>Company: </strong> <?php echo (strlen($property['name']) > 0) ? anchor(base_url('company/view/' . $property['company_id']), $property['name'], 'class="link-class"') : '' ?>
					<br>
					<strong>Contact: </strong> <?php echo (strlen($property['first_name'] . $property['last_name']) > 0) ? anchor(base_url('contact/view/' . $property['contact_id']), $property['first_name'] . ' ' . $property['last_name'], 'class="link-class"') : '' ?>
					<br>
					<br>

					<br><br>
					</p>
				</div>
			</div>
			<!-- /Content -->
		</div>
	</div>
</div>
