<div class="card mb-4 ">
    <div class="card-header with-elements">
        <h5 class="card-header-title mr-2 m-0">Properties</h5>

    </div>
    <div class="card-datatable table-responsive">
        <div class="col-lg-12" style="float:right;">
            <ul class="pagination pagination-sm mb-0" style="float:right">
                <?php if (@$hasPrevious) : ?>
                    <li class="page-item">
                        <a class="page-link btn-outline-secondary" href="<?= (isset($previousRecordLink)) ? $previousRecordLink : base_url('contact/view/' . @$contact['contact_id'] . "?type=previous"); ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php if (@$hasNext) : ?>
                    <li class="page-item">
                        <a class="page-link btn-outline-secondary ml-2" href="<?= (isset($nextRecordLink)) ? $nextRecordLink : base_url('contact/view/' . @$contact['contact_id'] . "?type=next"); ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <table class="table table-striped inner-datatable table-bordered nowrap">
            <thead>
                <tr>
                    <th>#</th>                   
                    <th>Availability Status Update Date</th>
                    <th>Availability Status</th>
                    <th>Tenant Name</th>
                    <th>Street Address</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Annual Rent/NOI</th>
                    <th>Asking Cap Rate</th>
                    <th>Asking Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contact['properties'] as $property) { ?>
                    <tr>
                        <td><?php echo @$offset += 1; ?></td>                        
                        <td><?php echo formatDate($property['availability_update_date']); ?></td>
                        <td><?php echo htmlspecialchars($property['availability_status']); ?></td>
                        <td><?php echo anchor(base_url('property/view/' . $property['property_id']), $property['name'], 'class="link-class"')
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars(@$property['address']); ?></td>
                        <td><?php echo htmlspecialchars(@$property['city']); ?></td>
                        <td><?php echo htmlspecialchars(@$property['state']); ?></td>
                        <td><?php echo moneyFormat(floatval(@$property['annual_rent']), "USD"); ?></td>
                        <td><?php echo number_format(floatval(@$property['asking_cap_rate']), 2) . "%"; ?></td>
                        <td><?php echo "$" . number_format(floatval(@$property['asking_price']), 0); ?></td>

                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>