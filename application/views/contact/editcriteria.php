<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row mb-3">
        <div class="col-lg-10">
            <a href="javascript:history.go(-1)" class="pb-5 text-secondary"><i class="ion ion-ios-arrow-back mr-2"></i> Back </a>
        </div>


    </div>
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">
            <div class="card w-100 mb-4 ">

                <!-- Content -->
                <div class="card-body col-lg-12">
                    <?php echo form_open(base_url("contact/edit_criteria/" . $criteria->criteria_id . "?page-type=$pageType"), array('method' => 'post')) ?>

                    <div class="form-row">
                        <input type="hidden" value="<?= $pageType ?>" name="page_type" />
                        <div class="form-group col-md-4">
                            <label class="form-label">Availability Status:</label>
                            <select class="form-control select2-availability-type" style="width:100%" placeholder="Availability Status" name="availability_status[]">
                                <option value="<?= AvailabilityStatus::OFF_MARKET ?>" <?= ($criteria->availability_status == AvailabilityStatus::OFF_MARKET) ? "selected='selected'" : "" ?>>
                                    Off Market
                                </option>
                                <option value="<?= AvailabilityStatus::ON_MARKET ?>" <?= ($criteria->availability_status == AvailabilityStatus::ON_MARKET) ? "selected='selected'" : "" ?>>
                                    On Market
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="form-label">Buyer Status:</label>
                            <select class="form-control select2-buyer-status" style="width:100%" placeholder="Buyer Status" name="buyer_status[]">
                                <option value="<?= BuyerStatus::ACTIVE ?>" <?= (set_value('buyer_status') == BuyerStatus::ACTIVE) ? "selected='selected'" : "" ?>>
                                    Leads
                                </option>
                                <option value="<?= BuyerStatus::PIPELINE ?>" <?= (set_value('buyer_status') == BuyerStatus::PIPELINE) ? "selected='selected'" : "" ?>>
                                    Pipeline
                                </option>
                            </select>
                            <div class="text-danger"><?= form_error('buyer_status') ?></div>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="form-label">Property Type</label>
                            <select class="select2-property-types form-control " style="width: 100%;background-color: white!important;" name="property_type[]">
                                <?php $propertyTypes = explode(", ", $criteria->property_type);
                                if (strlen($criteria->property_type) > 0) {
                                    foreach ($propertyTypes as $type) {
                                        echo '<option value="' . htmlspecialchars($type) . '">' . $type . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <div class="text-danger"><?= form_error('property_type') ?></div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">Price Range: <span class="text-muted"><span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Min Price" name="min_asking_price" value="<?= htmlspecialchars($criteria->min_asking_price) ?>">
                                <div class="input-group-append">
                                    <input type="text" class="form-control" placeholder="Max Price" name="max_asking_price" value="<?= htmlspecialchars($criteria->max_asking_price) ?>">
                                </div>
                            </div>
                            <div class="text-danger"><?= form_error('min_asking_price') ?></div>
                            <div class="text-danger"><?= form_error('max_asking_price') ?></div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">Minimum Asking Cap Rate: <span class="text-muted"><span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Min Rate" name="min_asking_rate" value="<?= htmlspecialchars($criteria->min_asking_rate) ?>">
                            </div>
                            <div class="text-danger"><?= form_error('min_asking_rate') ?></div>
                            <div class="text-danger"><?= form_error('max_asking_rate') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label">Minimum Lease Term Remaining:</label>
                            <input type="text" name="lease_term_remaining" class="form-control" value="<?= htmlspecialchars($criteria->lease_term_remaining) ?>" placeholder="Lease Term Remaining">
                            <div class="text-danger"><?= form_error('lease_term_remaining') ?></div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">Landlord Responsibilities:</label>
                            <select class="form-control select2-landlord_responsibilities" style="width:100%" placeholder="Availability Status" name="landlord_responsibilities[]">
                                <option value="NNN" <?= (set_value('landlord_responsibilities') == 'NNN') ? "selected='selected'" : "" ?>>NNN</option>
                                <option value="NN" <?= (set_value('landlord_responsibilities') == 'NN') ? "selected='selected'" : "" ?>>NN</option>
                                <option value="Gross" <?= (set_value('landlord_responsibilities') == 'Gross') ? "selected='selected'" : "" ?>>Gross</option>
                                <option value="Ground" <?= (set_value('landlord_responsibilities') == 'Ground') ? "selected='selected'" : "" ?>>Ground</option>
                                <option value="Leasehold" <?= (set_value('landlord_responsibilities') == 'Leasehold') ? "selected='selected'" : "" ?>>Leasehold</option>

                            </select>
                            <div class="text-danger"><?= form_error('landlord_responsibilities') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label">Tenant Name: <?= htmlspecialchars($criteria->tenant_name) ?></label>
                            <select class="form-control select2-tenants" style="width:100%" placeholder="Tenants" name="tenants[]">
                                <?php $tenants = explode(", ", $criteria->tenant_name);
                                if (strlen($criteria->tenant_name) > 0) {
                                    foreach ($tenants as $tenant) {
                                        echo '<option value="' . htmlspecialchars($tenant) . '">' . $tenant . '</option>';
                                    }
                                }

                                ?>
                            </select>
                            <div class="text-danger"><?= form_error('tenants') ?></div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">State: <?= htmlspecialchars($criteria->states) ?></label>
                            <select class="form-control select2-states" style="width:100%" placeholder="States" name="states[]">
                                <?php $states = explode(", ", $criteria->states);
                                if (strlen($criteria->states) > 0) {
                                    foreach ($states as $state) {
                                        echo "<option value='$state'>$state</option>";
                                    }
                                }

                                ?>
                            </select>
                            <div class="text-danger"><?= form_error('states') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Comment:</label>
                            <textarea name="comment" class="form-control" placeholder="Comment"><?= htmlspecialchars($criteria->comment) ?></textarea>
                            <div class="text-danger"><?= form_error('comment') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Acquisition Criteria Update Date:</label>
                            <input type="text" name="criteria_update_date" id="datepicker-base" class="form-control date-picker" value="<?= (strlen($criteria->criteria_update_date) > 4) ? date("m/d/Y", strtotime($criteria->criteria_update_date)) : null; ?>" placeholder="Acquisition Criteria Update Date">
                            <div class="text-danger"><?= form_error('criteria_update_date') ?></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
                <!-- /Content -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {

        $('.select2-availability-type')
            .wrap('<div class="position-relative"></div>')
            .select2({
                placeholder: 'Select',
                multiple: true,
                tags: true
            }).val(<?= json_encode(explode(", ", $criteria->availability_status)) ?>).trigger('change');

        $('.select2-buyer-status')
            .wrap('<div class="position-relative"></div>')
            .select2({
                placeholder: 'Select',
                multiple: true,
                tags: true
            }).val(<?= json_encode(explode(", ", $criteria->buyer_status)) ?>).trigger('change');

        $('.select2-landlord_responsibilities')
            .wrap('<div class="position-relative"></div>')
            .select2({
                placeholder: 'Select',
                multiple: true,
                tags: true
            }).val(<?= json_encode(explode(", ", $criteria->landlord_responsibilities)) ?>).trigger('change');

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
            }).val(<?= json_encode(explode(", ", $criteria->states)) ?>).trigger('change');

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
            }).val(<?= json_encode(explode(", ", $criteria->tenant_name)) ?>).trigger('change');

        $('.select2-property-types')
            .wrap('<div class="position-relative"></div>')
            .select2({
                placeholder: 'Property Type',
                multiple: true,
                tags: true,
                ajax: {
                    url: '<?php echo base_url("property/property_types") ?>',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        var option = {
                            id: 'Retail Single-Tenant',
                            text: 'Retail Single-Tenant'
                        };
                        data.push(option);

                        var option = {
                            id: 'Retail Multi-Tenant',
                            text: 'Retail Multi-Tenant'
                        };
                        data.push(option);

                        return {
                            results: data
                        };
                    }
                }
            }).val(<?= json_encode(explode(", ", $criteria->property_type)) ?>).trigger('change');
    });
</script>
