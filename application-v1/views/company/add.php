<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">

            <!-- Content -->
            <div class="card w-100 mb-4 ">
                <div class="card-header with-elements">
                    <h5 class="card-header-title mr-2 m-0"><?php echo $pageTitle; ?></h5>
                </div>
                <div class="card-body col-lg-9">
                    <?php echo form_open(base_url("company/add"), array('method' => 'post')) ?>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="<?= set_value('company_name') ?>" placeholder="Company Name">
                            <div class="text-danger"><?= form_error('company_name') ?></div>
                        </div>                            
                    </div>
                    <fieldset>
                        <legend>Company Address</legend>
                        <div id="company-address-container">
                            <div class="company-address-row">
                                <div class="form-group">
                                    <label class="form-label">Street Address</label>
                                    <input type="text" name="address[0][address]" value="<?= set_value('address[0][address]') ?>" class="form-control" placeholder="Street Address">
                                    <div class="text-danger"><?= form_error('address[0][address]') ?></div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-3">                                        
                                        <label class="form-label">City</label>
                                        <input type="text" name="city" placeholder="City" value="<?= set_value('address[0][city]') ?>" class="form-control">
                                        <div class="text-danger"><?= form_error('address[0][city]') ?></div>
                                    </div>
                                    
                                    <div class="form-group col-md-5">                                        
                                        <label class="form-label">State</label>
                                        <input type="text" name="address[0][state]" placeholder="State" value="<?= set_value('address[0][state]') ?>" class="form-control">
                                        <div class="text-danger"><?= form_error('address[0][state]') ?></div>
                                    </div>
                                    
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Zip Code</label>
                                        <input type="text" name="address[0][zip_code]" value="<?= set_value('address[0][zip_code]') ?>" class="form-control" placeholder="Zip Code">
                                        <div class="text-danger"><?= form_error('address[0][zip_code]') ?></div>
                                    </div>
                                    <div class="form-group col-md-1 mt-4">
                                       <a href="javascript:void(0)" class="btn btn-outline-danger remove-company-address-row ml-2" ><i class="fa fa-times"></i></a>
                                    </div>
                                </div>                                
                            </div>                            
                        </div>
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-dark mb-5" id="add-company-address"> + Add address</a>
                    </fieldset>
                    
                    <fieldset>
                        <legend>Company Phone</legend>
                        <div id="company-phone-container">
                            <div class="company-phone-row">
                                <div class="row">
                                    <div class="form-group col-lg-11">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phones[0][mobile]" value="<?= set_value('phones[0][mobile]') ?>" class="form-control" placeholder="Phone">
                                        <div class="text-danger"><?= form_error('phones[0][mobile]') ?></div>
                                    </div> 
                                    <div class="col-lg-1 mt-4">
                                        <a href="javascript:void(0)" class="btn btn-outline-danger remove-company-phone-row ml-2" ><i class="fa fa-times"></i></a>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-dark mb-5" id="add-company-phone"> + Add Phone</a>
                    </fieldset>
                    
                    <button type="submit" class="btn btn-primary">Save</button>
                    </form>             
                </div>
            </div>
            <!-- /Content -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
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