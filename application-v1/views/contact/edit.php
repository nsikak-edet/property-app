<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">

            <!-- Content -->
            <div class="card w-100 mb-4 ">
                <div class="card-header with-elements">
                    <h5 class="card-header-title mr-2 m-0"><?php echo $pageTitle; ?></h5>
                </div>
                <div class="card-body col-lg-9">
                    <?php echo form_open(base_url("contact/edit/" . $contact['contact_id']), array('method' => 'post')) ?>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($contact['first_name']) ?>" placeholder="First Name">
                            <div class="text-danger"><?= form_error('first_name') ?></div>
                        </div>                       
                        <div class="form-group col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($contact['last_name']) ?>" placeholder="Last Name">
                            <div class="text-danger"><?= form_error('last_name') ?></div>
                        </div>                 
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Lead Gen Type</label>
                            <select name="lead_gen_type" class="select2-property-types form-control "
                                    style="width: 100%;background-color: white!important;"
                                     name="lead_gen_type">
                                <option value='' >Select Type</option>
                                <option <?php echo (strtolower($contact['lead_gen_type']) == "met") ? "selected='selected" : "" ?> value='Met'>Met</option>
                                <option <?php echo (strtolower($contact['lead_gen_type']) == "haven't met") ? "selected='selected" : "" ?> value="Haven't Met">Haven't Met</option>
                            </select>          
                            <div class="text-danger"><?= form_error('lead_gen_type') ?></div>
                        </div> 
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Email</label>
                            <input type="text" name="email" class="form-control" value="<?= htmlspecialchars($contact['email']) ?>" placeholder="Email">
                            <div class="text-danger"><?= form_error('email') ?></div>
                        </div> 
                    </div>
                    
                    <fieldset>
                        <legend>Contact Address</legend>
                        <div id="contact-address-container">
                            <?php
                            $addressRows = $contact['addresses'];
                            $counter = 0;
                            if (is_array($addressRows)): foreach ($addressRows as $address):
                                    ?>
                                    <div class="contact-address-row">
                                        <div class="form-group">
                                            <label class="form-label">Street Address</label>
                                            <input type="text" name="address[<?= $counter ?>][address]" value="<?= htmlspecialchars(@$address->address); ?>" class="form-control" placeholder="Street Address">
                                            <div class="text-danger"><?= form_error('address[' . $counter . '][address]') ?></div>
                                        </div>

                                        <div class="form-row">
                                             <div class="form-group col-md-3">                                        
                                                <label class="form-label">City</label>
                                                <input type="text" name="address[<?= $counter ?>][city]" placeholder="City" value="<?= htmlspecialchars(@$address->city); ?>" class="form-control">
                                                <div class="text-danger"><?= form_error('address[' . $counter . '][city]') ?></div>
                                            </div>
                                            
                                            <div class="form-group col-md-5">                                        
                                                <label class="form-label">State</label>
                                                <input type="text" name="address[<?= $counter ?>][state]" placeholder="State" value="<?= htmlspecialchars(@$address->state); ?>" class="form-control">
                                                <div class="text-danger"><?= form_error('address[' . $counter . '][state]') ?></div>
                                            </div>
                                            
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Zip Code</label>
                                                <input type="text" name="address[<?= $counter ?>][zip_code]" value="<?= htmlspecialchars(@$address->zip_code); ?>" class="form-control" placeholder="Zip Code">
                                                <div class="text-danger"><?= form_error('address[' . $counter . '][zip_code]') ?></div>
                                            </div>
                                            <div class="form-group col-md-1 mt-4">
                                                <a href="javascript:void(0)" class="btn btn-outline-danger remove-contact-address-row ml-2" ><i class="fa fa-times"></i></a>
                                            </div>
                                        </div>                                
                                    </div>  
        <?php $counter++;
    endforeach;
endif; ?>
                        </div>
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-dark mb-5" id="add-contact-address"> + Add address</a>
                    </fieldset>

                    <fieldset>
                        <legend>Contact Phone(s)</legend>
                        <div id="contact-phone-container">
                            <?php
                            $phoneRows = $contact['phones'];
                            $counter = 0;
                            if (is_array($phoneRows)): foreach ($phoneRows as $phone):
                                    ?>
                                    <div class="contact-phone-row">                                
                                        <div class="row">
                                            <div class="form-group col-lg-11">
                                                <label class="form-label">Phone</label>
                                                <input type="text" name="phones[<?= $counter ?>][mobile]" value="<?= htmlspecialchars(@$phone->phone); ?>" class="form-control" placeholder="Phone">
                                                <div class="text-danger"><?= form_error('phones[' . $counter . '][mobile]') ?></div>
                                            </div> 
                                            <div class="col-lg-1 mt-4">
                                                <a href="javascript:void(0)" class="btn btn-outline-danger remove-contact-phone-row ml-2" ><i class="fa fa-times"></i></a>
                                            </div>
                                        </div>                              
                                    </div>
                                    <?php $counter++;
                                endforeach;
                            endif; ?>
                        </div>
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-dark mb-5" id="add-contact-phone"> + Add Phone</a>
                    </fieldset>
                    
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Company <?= htmlspecialchars("(" . $contact['company_name'] . ")") ?></label>
                            <select class="select2-company form-control"
                                    style="width: 100%;background-color: white!important;"
                                    data-allow-clear="true" name="company_name">
                            </select>
                            <span class="text-danger"><?php echo strip_tags(form_error('company_name')) ?></span>
                        </div>
                    </div>

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
        $('a#add-contact-address').cloneData({
            mainContainerId: 'contact-address-container', // Main container Should be ID
            cloneContainer: 'contact-address-row', // Which you want to clone
            removeButtonClass: 'remove-contact-address-row', // Remove button for remove cloned HTML
            removeConfirm: true, // default true confirm before delete clone item
            removeConfirmMessage: 'Are you sure want to delete?', // confirm delete message
            minLimit: 1, // Default 1 set minimum clone HTML required
            maxLimit: 15, // Default unlimited or set maximum limit of clone HTML
            excludeHTML: ".exclude", // remove HTML from cloned HTML
            defaultRender: 1, // Default 1 render clone HTML
            init: function () {
            },
            beforeRender: function () {
            },
            afterRender: function () {
            },
            afterRemove: function () {
            },
            beforeRemove: function () {
                console.warn(':: Before remove callback called');
            }
        });

        $('a#add-contact-phone').cloneData({
            mainContainerId: 'contact-phone-container', // Main container Should be ID
            cloneContainer: 'contact-phone-row', // Which you want to clone
            removeButtonClass: 'remove-contact-phone-row', // Remove button for remove cloned HTML
            removeConfirm: true, // default true confirm before delete clone item
            removeConfirmMessage: 'Are you sure want to delete?', // confirm delete message
            minLimit: 1, // Default 1 set minimum clone HTML required
            maxLimit: 15, // Default unlimited or set maximum limit of clone HTML
            excludeHTML: ".exclude", // remove HTML from cloned HTML
            defaultRender: 1, // Default 1 render clone HTML
            init: function () {
            },
            beforeRender: function () {
            },
            afterRender: function () {
            },
            afterRemove: function () {
            },
            beforeRemove: function () {
                console.warn(':: Before remove callback called');
            }
        });
    });

</script>

<script type="text/javascript">
    $(function () {
        $('.select2-company')
                .wrap('<div class="position-relative"></div>')
                .select2({
                    placeholder: 'Search Company',
                    multiple: false,
                    ajax: {
                        url: '<?php echo base_url("contact/all") ?>',
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        }
                    },
                    tags: true
                });

    });
</script>

