<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">

            <!-- Content -->
            <div class="card w-100 mb-4 ">
                <div class="card-header with-elements">
                    <h5 class="card-header-title mr-2 m-0"><?php echo $pageTitle; ?></h5>
                </div>
                <div class="card-body col-lg-9">
                    <?php echo form_open(base_url("property/edit/" . $property['property_id']), array('method' => 'post')) ?>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label class="form-label">Tenant Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($property['property_name']) ?>" placeholder="Tenant Name">
                            <div class="text-danger"><?= form_error('name') ?></div>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="form-label">Property Type: <?= htmlspecialchars($property['property_type']) ?></label>
                            <select class="select2-property-types form-control " style="width: 100%;background-color: white!important;" name="property_type">
                            </select>
                            <div class="text-danger"><?= form_error('property_type') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Tax Record Sent</label>
                            <input type="text" name="tax_record_sent_date" id="datepicker-base" class="form-control date-picker" value="<?= ($property['tax_record_sent_date'] != null) ? formatDate(@$property['tax_record_sent_date']) : ''; ?>" placeholder="Tax record sent date">
                            <div class="text-danger"><?= form_error('tax_record_sent_date') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Last Update Date</label>
                            <input type="text" name="last_update" id="datepicker-base" class="form-control date-picker" value="<?= ($property['last_update'] != null) ? formatDate(@$property['last_update']) : ''; ?>" placeholder="Last update date">
                            <div class="text-danger"><?= form_error('last_update') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Store #</label>
                            <input type="text" name="store_number" class="form-control" value="<?= htmlspecialchars($property['store_number']) ?>" placeholder="Store Number">
                            <div class="text-danger"><?= form_error('store_number') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Google Maps Link</label>
                            <input type="text" name="google_map_link" class="form-control" value="<?= htmlspecialchars($property['google_map_link']) ?>" placeholder="Link to property in google maps">
                            <div class="text-danger"><?= form_error('google_map_link') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label">Availability Status</label>
                            <select class="select2-availability form-control " multiple style="width: 100%;background-color: white!important;" name="availability_status[]">
                                <option value="Off Market">Off Market</option>
                                <option value="On Market">On Market</option>
                                <option value="Under LOI">Under LOI</option>
                                <option value="Under Contract">Under Contract</option>
                                <option value="Pipeline">Pipeline</option>
                            </select>
                            <div class="text-danger"><?= form_error('availability_status') ?></div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">Availability Status Update Date</label>
                            <input type="text" name="availability_update_date" id="datepicker-base" class="form-control date-picker" value="<?= ($property['availability_update_date'] != null) ? formatDate(@$property['availability_update_date']) : ''; ?>" placeholder="Availability Status Update Date">
                            <div class="text-danger"><?= form_error('availability_update_date') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label class="form-label">Lease Type</label>
                            <select class="form-control" placeholder="Lease Type" name="lease_type">
                                <option value="">Select Option</option>
                                <option value="NNN" <?= ($property['lease_type'] == "NNN") ? "selected='selected'" : "" ?>>NNN</option>
                                <option value="NN" <?= ($property['lease_type'] == "NN") ? "selected='selected'" : "" ?>>NN</option>
                                <option value="Gross" <?= ($property['lease_type'] == "Gross") ? "selected='selected'" : "" ?>>Gross</option>
                                <option value="Roof & Structure" <?= ($property['lease_type'] == "Roof & Structure") ? "selected='selected'" : "" ?>>Roof & Structure</option>
                                <option value="Ground" <?= ($property['lease_type'] == "Ground") ? "selected='selected'" : "" ?>>Ground</option>
                                <option value="Leasehold" <?= ($property['lease_type'] == "Leasehold") ? "selected='selected'" : "" ?>>Leasehold</option>
                            </select>
                            <div class="text-danger"><?= form_error('lease_type') ?></div>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="form-label">Annual Rent/NOI</label>
                            <input type="text" name="annual_rent" class="form-control" value="<?= htmlspecialchars($property['annual_rent']) ?>" placeholder="Annual Rent">
                            <div class="text-danger"><?= form_error('annual_rent') ?></div>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="form-label">Asking Cap Rate</label>
                            <input type="text" name="asking_cap_rate" class="form-control" value="<?= htmlspecialchars($property['asking_cap_rate']) ?>" placeholder="Asking Cap Rate">
                            <div class="text-danger"><?= form_error('asking_cap_rate') ?></div>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="form-label">Asking Price</label>
                            <input type="text" name="asking_price" class="form-control" value="<?= htmlspecialchars($property['asking_price']) ?>" placeholder="Asking Price">
                            <div class="text-danger"><?= form_error('asking_price') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label class="form-label">Lease Commencement Date</label>
                            <input type="text" id="datepicker-base" name="lease_commencement_date" class="form-control date-picker" value="<?= ($property['lease_commencement_date'] != null) ? formatDate(@$property['lease_commencement_date']) : ''; ?>" placeholder="Lease Commencement Date">
                            <div class="text-danger"><?= form_error('lease_commencement_date') ?></div>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="form-label">Lease Expiration Date</label>
                            <input type="text" id="datepicker-base" name="lease_expiration_date" class="form-control date-picker" value="<?= ($property['lease_expiration_date'] != null) ? formatDate(@$property['lease_expiration_date']) : ''; ?>" placeholder="Lease Expiration Date">
                            <div class="text-danger"><?= form_error('lease_expiration_date') ?></div>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="form-label">Building Size</label>
                            <input type="text" name="building_size" class="form-control" value="<?= htmlspecialchars($property['building_size']) ?>" placeholder="Building Size">
                            <div class="text-danger"><?= form_error('building_size') ?></div>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="form-label">Land Size</label>
                            <input type="text" name="land_size" class="form-control" value="<?= htmlspecialchars($property['land_size']) ?>" placeholder="Land Size">
                            <div class="text-danger"><?= form_error('land_size') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Property Link</label>
                            <input type="text" name="property_link" class="form-control" value="<?= htmlspecialchars($property['property_link']) ?>" placeholder="Property Link">
                            <div class="text-danger"><?= form_error('property_link') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Comments</label>
                            <textarea name="comments" class="form-control"><?= htmlspecialchars($property['comments']) ?></textarea>
                            <div class="text-danger"><?= form_error('comments') ?></div>
                        </div>
                    </div>

                    <fieldset>
                        <legend>Property Address</legend>
                        <div id="company-address-container">
                            <div class="company-address-row">
                                <div class="form-group">
                                    <label class="form-label">Street Address</label>
                                    <input type="text" name="address" value="<?= htmlspecialchars($property['address']) ?>" class="form-control" placeholder="Street Address">
                                    <div class="text-danger"><?= form_error('address') ?></div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label class="form-label">City</label>
                                        <input type="text" name="city" placeholder="City" value="<?= htmlspecialchars($property['city']) ?>" class="form-control">
                                        <div class="text-danger"><?= form_error('city') ?></div>
                                    </div>

                                    <div class="form-group col-md-5">
                                        <label class="form-label">State</label>
                                        <input type="text" name="state" placeholder="State" value="<?= htmlspecialchars($property['state']) ?>" class="form-control">
                                        <div class="text-danger"><?= form_error('state') ?></div>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label class="form-label">Zip Code</label>
                                        <input type="text" name="zip_code" value="<?= htmlspecialchars($property['zip_code']) ?>" class="form-control" placeholder="Zip Code">
                                        <div class="text-danger"><?= form_error('zip_code') ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Property Owner</legend>
                        <div class="company-address-row">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label class="form-label">Company : <?= htmlspecialchars($property['name']) ?></label>
                                    <select class="select2-property-company form-control " style="width: 100%;background-color: white!important;" name="company">
                                    </select>
                                    <div class="text-danger"><?= form_error('company') ?></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label class="form-label">Contact : <?= htmlspecialchars($property['first_name'] . " " . $property['middle_name'] . " " . $property['last_name']) ?></label>
                                    <select class="select2-property-contact form-control " style="width: 100%;background-color: white!important;" name="contact_id">
                                    </select>
                                    <div class="text-danger"><?= form_error('contact_id') ?></div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
            <!-- /Content -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('.select2-property-types')
            .wrap('<div class="position-relative"></div>')
            .select2({
                placeholder: 'Property Type',
                multiple: false,
                tags: true,
                ajax: {
                    url: '<?php echo base_url("property/property_types") ?>',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });

        $('.select2-property-company')
            .wrap('<div class="position-relative"></div>')
            .select2({
                placeholder: 'Search Company Name',
                multiple: false,
                allowClear: true,
                tags: true,
                ajax: {
                    url: '<?php echo base_url("company/companies") ?>',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });

        $('.select2-property-contact')
            .wrap('<div class="position-relative"></div>')
            .select2({
                placeholder: 'Search Contact',
                multiple: false,
                allowClear: true,
                tags: true,
                ajax: {
                    url: '<?php echo base_url("property/search_contacts") ?>',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            $('.select2-availability')
            .wrap('<div class="position-relative"></div>')
            .select2({
                placeholder: 'Select',
                multiple: true,
                allowClear: true,
                tags: true
            }).val(<?= (strlen($property['availability_status']) > 0) ? json_encode(explode(",", $property['availability_status'])) : '' ; ?>).trigger('change');

    });
</script>