<div class="card mb-4 ">
    <div class="card-header with-elements">
        <h5 class="card-header-title mr-2 m-0">Acquisition Criteria</h5>
        <a href="<?= base_url("contact/add_criteria/" . @$contact['contact_id'] . "?page-type=contact") ?>" class="btn btn-outline btn-primary btn-xs p--3 pr--5"><i class="fi fi-pencil pr-0 mr-0 ml-1"></i> Add</a>

    </div>

    <div class="card-body col-lg-12">
        <table class="table table-striped card-datatable inner-datatable table-bordered nowrap">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Contact Name</th>
                    <th>Availability Status</th>
                    <th>Buyer Status</th>
                    <th>Property Type</th>
                    <th>Min Price</th>
                    <th>Max Price</th>
                    <th>Min Cap Rate</th>                    
                    <th>Minimum Lease Term Remaining</th>
                    <th>Landlord Responsibilities</th>
                    <th>Tenant Name</th>
                    <th>States</th>
                    <th>Acquisition Criteria Update Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($criteriaList as $criteria) { ?>
                    <tr>
                        <td><?php echo @$offset += 1; ?></td>
                        <td><a href="<?= base_url("contact/view_criteria/" . $criteria->criteria_id) ?>"><?= htmlspecialchars($criteria->first_name . ' ' . $criteria->last_name); ?></a></td>
                        <td><?= htmlspecialchars($criteria->availability_status); ?></td>
                        <td><?= htmlspecialchars($criteria->buyer_status); ?></td>
                        <td><?= htmlspecialchars($criteria->property_type); ?></td>
                        <td><?= htmlspecialchars("$" . number_format($criteria->min_asking_price)); ?></td>
                        <td><?= htmlspecialchars("$" .  number_format($criteria->max_asking_price)); ?></td>                        
                        <td><?= htmlspecialchars($criteria->min_asking_rate . "%"); ?></td>                        
                        <td><?= htmlspecialchars($criteria->lease_term_remaining); ?></td>
                        <td><?= htmlspecialchars($criteria->landlord_responsibilities); ?></td>
                        <td><?= htmlspecialchars($criteria->tenant_name); ?></td>
                        <td><?= htmlspecialchars($criteria->states); ?></td>
                        <td><?= formatDate($criteria->criteria_update_date); ?></td>

                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="card-datatable table-responsive">
        <div class="col-lg-12" style="float:right;">
            <ul class="pagination pagination-sm mb-0" style="float:right">

            </ul>
        </div>

    </div>

</div>

<script type="text/javascript">
    $(function() {
        $('a#add-company-address').cloneData({
            mainContainerId: 'company-address-container', // Main container Should be ID
            cloneContainer: 'company-address-row', // Which you want to clone
            removeButtonClass: 'remove-company-address-row', // Remove button for remove cloned HTML
            removeConfirm: true, // default true confirm before delete clone item
            removeConfirmMessage: 'Are you sure want to delete?', // confirm delete message
            minLimit: 1, // Default 1 set minimum clone HTML required
            maxLimit: 15, // Default unlimited or set maximum limit of clone HTML
            excludeHTML: ".exclude", // remove HTML from cloned HTML
            defaultRender: 1, // Default 1 render clone HTML
            init: function() {},
            beforeRender: function() {},
            afterRender: function() {},
            afterRemove: function() {},
            beforeRemove: function() {
                console.warn(':: Before remove callback called');
            }
        });

        $('a#add-company-phone').cloneData({
            mainContainerId: 'company-phone-container', // Main container Should be ID
            cloneContainer: 'company-phone-row', // Which you want to clone
            removeButtonClass: 'remove-company-phone-row', // Remove button for remove cloned HTML
            removeConfirm: true, // default true confirm before delete clone item
            removeConfirmMessage: 'Are you sure want to delete?', // confirm delete message
            minLimit: 1, // Default 1 set minimum clone HTML required
            maxLimit: 15, // Default unlimited or set maximum limit of clone HTML
            excludeHTML: ".exclude", // remove HTML from cloned HTML
            defaultRender: 1, // Default 1 render clone HTML
            init: function() {},
            beforeRender: function() {},
            afterRender: function() {},
            afterRemove: function() {},
            beforeRemove: function() {
                console.warn(':: Before remove callback called');
            }
        });
    });
</script>

<script type="text/javascript">
    $(function() {
        $('.select2-company')
            .wrap('<div class="position-relative"></div>')
            .select2({
                placeholder: 'Search Company',
                multiple: false,
                ajax: {
                    url: '<?php echo base_url("contact/all") ?>',
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

        $('.select2-availability-type')
            .wrap('<div class="position-relative"></div>')
            .select2({
                placeholder: 'Select',
                multiple: true,
                tags: true
            }).val(<?= json_encode(explode(",", $contact['availability_status'])) ?>).trigger('change');

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
            }).val(<?= json_encode(explode(",", $contact['states'])) ?>).trigger('change');

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
            }).val(<?= json_encode(explode(",", $contact['tenant_name'])) ?>).trigger('change');

    });
</script>