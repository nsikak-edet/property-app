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
                            <label class="form-label">Property Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($property['property_name']) ?>" placeholder="Property Name">
                            <div class="text-danger"><?= form_error('name') ?></div>
                        </div> 

                        <div class="form-group col-md-4">
                            <label class="form-label">Property Type: <?= htmlspecialchars($property['property_type']) ?></label>
                            <select class="select2-property-types form-control "
                                    style="width: 100%;background-color: white!important;"
                                    name="property_type">
                            </select>   
                            <div class="text-danger"><?= form_error('property_type') ?></div>
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
                                    <select class="select2-property-company form-control "
                                            style="width: 100%;background-color: white!important;"
                                            name="company">
                                    </select>                            
                                    <div class="text-danger"><?= form_error('company') ?></div>
                                </div> 
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label class="form-label">Contact : <?= htmlspecialchars($property['first_name'] . " " . $property['middle_name'] . " " . $property['last_name']) ?></label>
                                    <select class="select2-property-contact form-control "
                                            style="width: 100%;background-color: white!important;"
                                            name="contact_id">
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
    $(function () {
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
                        processResults: function (data) {
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
                        processResults: function (data) {
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
                    tags: false,
                    ajax: {
                        url: '<?php echo base_url("property/search_contacts") ?>',
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        }
                    }
                });

    });
</script>

