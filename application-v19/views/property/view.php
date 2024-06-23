<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row mb-3">
        <div class="col-lg-10">
            <a href="javascript:history.go(-1)" class="pb-5 text-secondary"><i class="ion ion-ios-arrow-back mr-2"></i> Back </a>
        </div>


    </div>
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">

            <!-- Content -->
            <div class="card w-100 mb-4 ">
                <div class="card-header with-elements">
                    <h5 class="card-header-title m-0 col-lg-10"><?php echo $pageTitle; ?>
                        <a href="<?php echo base_url('property/edit/' . $property['property_id']); ?>" class="btn btn-outline btn-primary btn-xs p--3 pr--5"><i class="fi fi-pencil pr-0 mr-0 ml-1"></i> Edit</a>
                        <a href="<?php echo base_url('property/remove/' . $property['property_id']); ?>" name="property" class="btn btn-outline btn-outline-danger btn-xs delete-button-confirm p--3 pr--5 mb-"><i class="fi fi-pencil pr-0 mr-0 ml-1"></i> Delete</a>
                    </h5>
                </div>

                <div class="card-body col-lg-12">
                    <div class="col-lg-12" style="float:right;">
                        <?php if (@$hideNext == false) : ?>
                            <ul class="pagination pagination-sm mb-0" style="float:right">
                                <?php if (@$hasPrevious) : ?>
                                    <li class="page-item">
                                        <a class="page-link btn-outline-secondary" href="<?php echo base_url('property/view/' . $property['property_id']); ?>?type=previous">Previous</a>
                                    </li>
                                <?php endif; ?>

                                <?php if (@$hasNext) : ?>
                                    <li class="page-item">
                                        <a class="page-link btn-outline-secondary ml-2" href="<?php echo base_url('property/view/' . $property['property_id']); ?>?type=next">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <p>
                        <strong>Tenant Name: </strong> <?= htmlspecialchars($property['property_name']) ?><br>
                        <strong>Property Type: </strong> <?= htmlspecialchars($property['property_type']) ?><br>
                        <strong>Store #: </strong> <?= htmlspecialchars($property['store_number']) ?><br>
                        <strong>RPR: </strong> <?= getPPRLink($property['address'] . ", " . $property['city'] . ", " . $property['state'] . " " . $property['zip_code']); ?><br>
                        <strong>Google Map: </strong> <?= (strlen($property['google_map_link']) > 2) ? "<a href='" . prep_url(trim($property['google_map_link'])) . "' target='_blank'>" .  htmlspecialchars($property['google_map_link']) . "</a>" : '' ?><br>
                        <strong>Last Update Date: </strong> <?= ($property['last_update'] != null) ? formatDate(@$property['last_update']) : ''; ?><br>
                        <strong>Tax Record Sent Date: </strong> <?= ($property['tax_record_sent_date'] != null) ? formatDate(@$property['tax_record_sent_date']) : ''; ?><br><br>

                    <h3>Address</h3>
                    <table>
                        <tr>
                            <td style="width:250px"><strong>Street Address: </strong> </td>
                            <td><?= htmlspecialchars($property['address']) ?></td>
                        </tr>
                        <tr>
                            <td style="width:250px"><strong>City: </strong> </td>
                            <td><?= htmlspecialchars($property['city']) ?></td>
                        </tr>
                        <tr>
                            <td style="width:250px"><strong>State: </strong> </td>
                            <td><?= htmlspecialchars($property['state']) ?></td>
                        </tr>
                        <tr>
                            <td style="width:250px"><strong>Zip Code: </strong> </td>
                            <td><?= htmlspecialchars($property['zip_code']) ?></td>
                        </tr>
                    </table>

                    <h3>Owner</h3>
                    <strong>Company: </strong> <?php echo (strlen($property['name']) > 0) ? anchor(base_url('company/view/' . $property['company_id']), $property['name'], 'class="link-class"') : '' ?> <br>
                    <strong>Contact: </strong> <?php echo (strlen($property['first_name'] . $property['last_name']) > 0) ? anchor(base_url('contact/view/' . $property['contact_id']), $property['first_name'] . ' ' . $property['last_name'], 'class="link-class"') : '' ?><br>
                    <br>

                    <table>
                        <tr>
                            <td style="width:250px"><strong>Availability Status: </strong> </td>
                            <td><?= htmlspecialchars($property['availability_status']);  ?></td>
                        </tr>
                        <tr>
                            <td><strong>Availability Status Update Date: </strong></td>
                            <td><?= ($property['availability_update_date'] != null) ? formatDate(@$property['availability_update_date']) : ''; ?></td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <td style="width:250px"><strong>Lease Type: </strong></td>
                            <td><?= htmlspecialchars($property['lease_type']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Annual Rent/NOI: </strong></td>
                            <td><?= moneyFormat($property['annual_rent'], "USD"); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Asking Cap Rate: </strong></td>
                            <td><?= htmlspecialchars($property['asking_cap_rate'] . "%"); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Asking Price: </strong></td>
                            <td><?= "$" . number_format($property['asking_price']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Lease Commencement Date: </strong></td>
                            <td><?= ($property['lease_commencement_date'] != null) ? formatDate(@$property['lease_commencement_date']) : ''; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Lease Expiration Date: </strong></td>
                            <td><?= ($property['lease_expiration_date'] != null) ? formatDate(@$property['lease_expiration_date']) : ''; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Lease Term Remaining: </strong></td>
                            <td><?= ($property['lease_term_remaining']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Building Size: </strong></td>
                            <td><?= number_format($property['building_size'], 0); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Land Size: </strong></td>
                            <td><?= number_format($property['land_size'], 2); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Comment: </strong></td>
                            <td><?= htmlspecialchars($property['comments']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Property Link: </strong></td>
                            <td><?= (strlen($property['property_link']) > 2) ? "<a href='" . prep_url(trim($property['property_link'])) . "' target='_blank'>" .  htmlspecialchars($property['property_link']) . "</a>" : '' ?></td>
                        </tr>
                    </table>
                    <br><br>
                    </p>
                </div>
            </div>
            <!-- /Content -->
        </div>
    </div>
</div>