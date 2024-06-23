



<div class="container-fluid flex-grow-1 container-p-y pb-0">
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">
            <!-- Content -->
            <div class="card w-100 mb-1">
                <div class="card-body">
                    <?php echo form_open(base_url("property/"), array('method' => 'post', 'enctype' => 'multipart/form-data')) ?>
                    <div class="form-group">
                        <label class="form-label w-100">Property Upload File</label>
                        <input type="file" name="file">
                        <small class="form-text text-muted">Allowed files: .xls/xlsx only.</small>
                        <span class="text-danger"><?php echo @$uploadError; ?></span>
                        <a target='_blank' href="<?= base_url("/uploads/property-upload-temp.xlsx") ?>" class='mt-2'><i class='ios ion-ios-download '></i> download upload template</a>
                    </div>
                    <button type="submit" class="btn btn-default">
                        <i class='ion ion-ios-cloud-upload'></i>
                        Upload Properties</button>
                    </form>
                </div>
            </div>
            <!-- /Content -->
        </div>
    </div>
</div>


<div class="container-fluid flex-grow-1 container-p-y mt-0">
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">
            <!-- Content -->
            <div class="card w-100 mb-1">
                <div class="card-body">                    
                    <?php echo form_open(base_url("property/search/"), ['method' => 'post']); ?>
                    <div class="form-group col-12 mb-0 ">

                        <div class="input-group mb-1">
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->name); ?>" name="name" placeholder="search property by name">
                            <span class="input-group-append">
                            <button class="btn btn-default advance-search" type="button" data-toggle="collapse" href="#accordion-1" aria-expanded="true">Advanced</button>
                            <button class="btn btn-secondary" type="submit">Search</button>
                        </span>
                        </div>
                        <div id="accordion">
                            <div class="card mb-2 bg-transparent">
                                <div id="accordion-1" class="collapse <?php echo ($showAdvanceSearch) ? "show" : "" ?>" data-parent="#accordion" >
                                    <div class="card-body">
                                        
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label class="form-label">Store #</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->store_no); ?>" placeholder="Store #" name="store_no">
                                            </div>
                                            <div class="form-group col-md-8">
                                                <label class="form-label">Street Address</label>
                                                <input type="text" class="form-control" placeholder="Street Address" value="<?php echo htmlspecialchars(@$search->street_address); ?>" name="street_address">
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label class="form-label">City</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->city); ?>" placeholder="City" name="city">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="form-label">State</label>
                                                <input type="text" class="form-control" placeholder="State" value="<?php echo htmlspecialchars(@$search->state); ?>" name="state">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="form-label">Zip Code</label>
                                                <input type="text" class="form-control" placeholder="Zip Code" value="<?php echo htmlspecialchars(@$search->zip_code); ?>" name="zip_code">
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label class="form-label">Property Type</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->property_type); ?>" placeholder="Property Type" name="property_type">
                                            </div>
                                            
                                            <div class="form-group col-md-8">
                                                <label class="form-label">(contacts & companies) who own "x" amount of properties</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->property_count); ?>" placeholder="Enter count e.g. 1, 2 etc" name="property_count">
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">                                           
                                            <div class="form-group col-md-6">
                                                <label class="form-label">Contact</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->contact_name); ?>" placeholder="Enter contact first name or middle name or last name" name="contact_name">
                                            </div>
                                            
                                            <div class="form-group col-md-6">
                                                <label class="form-label">Company</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->company); ?>" placeholder="Enter company name" name="company" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">                                           
                                            <div class="form-group col-md-12">
                                                <label class="form-label">(Contacts & Companies) who own properties in state</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->property_state); ?>" placeholder="Enter state to search for property owners" name="property_state">
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="mt-2 col-lg-12">
                                                <button type="submit" class="btn btn-primary mt-3">Search</button>
                                                <a style="margin-left:5px;" href="<?php echo base_url('property/reset_form') ?>" class="btn btn-outline-secondary mt-3">Clear Options</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <!-- /Content -->
        </div>
    </div>
</div>


<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">

            <!-- Content -->
            <div class="card w-100 mb-4">
                <div class="card-header with-elements">
                    <h5 class="card-header-title mr-2 m-0 p-1"><?= $this->session->userdata('searchTitle') ?></h5>
                    
                    <div class="card-header-elements ml-md-auto">
                        <a href="<?php echo base_url("property/add") ?>" class="btn btn-xs btn-outline-primary">
                            <span class="ion ion-md-add"></span> Add Property</a>
                    </div>
                </div>
                <div class="card-datatable table-responsive">
                    <h4 class="pl-3 pt-2 pb-4">Total search results (<?= tofloat(sizeof($properties)) ?>)</h4>
                    <table class="table table-striped datatable table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>                              
                                <th>Name</th>
                                <th>Record Type</th>
                                <th>Street Address</th>
                                <th>State</th>
                                <th>City</th>
                                <th>Zip Code</th>                           
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($properties as $property) { ?>
                                <tr>
                                    <td><?php echo @$offset += 1; ?></td>                                    
                                    <td><a href="<?= ($property['record_type'] == 'contact') ? base_url('contact/view/' . $property['entity_id']) : base_url('company/view/' . $property['entity_id']) ; ?>"><?php echo htmlspecialchars($property['name']); ?></a></td>
                                    <td><?php echo htmlspecialchars($property['record_type']); ?></td>
                                    <td><?php echo htmlspecialchars(@$property['address']); ?></td>
                                    <td><?php echo htmlspecialchars(@$property['state']); ?></td>
                                    <td><?php echo htmlspecialchars(@$property['city']); ?></td>
                                    <td><?php echo htmlspecialchars(@$property['zip_code']); ?></td> 
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    

                </div>
            </div>
            <!-- /Content -->
        </div>
    </div>
</div>
