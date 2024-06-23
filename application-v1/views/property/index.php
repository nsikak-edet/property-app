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
                                            <div class="form-group col-md-6 ">

                                                <label class="form-label">State: <?php echo (is_array(@$search->state)) ? implode(', ', @$search->state) : ''; ?></label>
                                                <select data-allow-clear="true" name="state[]" class="select2-states" style="width:100%"></select>
<!--                                                <input type="text" class="form-control "  placeholder="for multiple, separate each sate by comma e.g state a, state b etc." value="" name="state">-->
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="form-label">Zip Code</label>
                                                <input type="text" class="form-control" placeholder="Zip Code" value="<?php echo htmlspecialchars(@$search->zip_code); ?>" name="zip_code">
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label class="form-label">(Contacts & Companies) who own "x" or "x-y" amount of properties</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->property_count); ?>" placeholder="Enter count e.g 1, 2, 3... and range e.g. 1-2,1-5 etc" name="property_count">
                                            </div>
                                        </div>

                                        <div class="form-row"> 
                                            <div class="form-group col-md-4">
                                                <label class="form-label">Lead Gen Type</label>
                                                <select class="form-control" value="<?php echo htmlspecialchars(@$search->contact_name); ?>" placeholder="Lead Gen Type" name="lead_gen_type">
                                                    <option value="">Select Type</option>
                                                    <option value="<?= LeadGenTypes::MET ?>" <?= (@$search->lead_gen_type == LeadGenTypes::MET) ? "selected='selected'" : "" ?>>Met or Haven't Met</option>
                                                    <option value="<?= LeadGenTypes::HAVENT_MET ?>" <?= (@$search->lead_gen_type == LeadGenTypes::HAVENT_MET) ? "selected='selected'" : "" ?>>Not a Met or Haven't Met</option>                                                    
                                                </select>

                                            </div>

                                            <div class="form-group col-md-4">
                                                <label class="form-label">Contact Name</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->first_name); ?>" placeholder="First Name" name="first_name">

                                                    <span class="input-group-append">
                                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->last_name); ?>" placeholder="Last Name" name="last_name">
                                                    </span>
                                                </div>


                                            </div>

                                            <div class="form-group col-md-4">
                                                <label class="form-label">Company</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->company); ?>" placeholder="Enter company name" name="company" >
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
                    <h5 class="card-header-title mr-2 m-0 badge badge-secondary p-1">Total Properties in Database(<?= tofloat($totalProperties) ?>)</h5>

                    <div class="card-header-elements ml-md-auto">
                        <a href="<?php echo base_url("property/add") ?>" class="btn btn-xs btn-outline-primary">
                            <span class="ion ion-md-add"></span> Add Property</a>
                    </div>
                </div>

                <div class="card-body">                    
                    <div class="nav-tabs-top ">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#navs-top-property"><i class="ion ion-ios-home mr-2"></i>Properties</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#navs-top-owner"><i class="ion ion-ios-contacts mr-2"></i> Owners</a>
                            </li>                            
                        </ul>
                        <div class="tab-content overflow-auto">
                            <div class="tab-pane fade active show" id="navs-top-property">
                                <div class="card-body">
                                    <table class="table nowrap search-datatable table-bordered" data-ordering="false">
                                        <thead>
                                            <tr class="bg-lighter">
                                                <th>#</th>
                                                <th>Property Name</th>                                 
                                                <th style="display:none">Lead Gen Type</th>
                                                <th>Property Type</th>
                                                <th>Owner Type</th>
                                                <th>Owner</th>                                
                                                <th>Store #</th>  
                                                <th>Street Address</th>
                                                <th>City</th>
                                                <th>State</th>
                                                <th>Zip Code</th>
                                                <th>Owner Email</th>     
                                                <th>Owner Phone #</th>      
                                                <th>Owner Street Address</th>      
                                                <th>Owner City</th>      
                                                <th>Owner State</th>      
                                                <th>Owner Zip Code</th>      
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach ($properties as $property) : ?>                                
                                                <?php foreach ($property['properties'] as $prop) : ?>
                                                    <tr>
                                                        <td><?php echo @$offset += 1; ?></td>
                                                        <td>
                                                            <?php echo anchor(base_url('property/view/' . $prop['property_id']), $prop['name'], 'class="link-class" target="_blank"')
                                                            ?></td>                                    
                                                        <td style="display:none"><?php echo htmlspecialchars($prop['lead_gen_type']); ?></td>
                                                        <td><?php echo htmlspecialchars($prop['property_type']); ?></td>
                                                        <td><?php echo htmlspecialchars(@$property['type']); ?></td>
                                                        <td>
                                                            <?php if (@$property['type'] == EntityTypes::COMPANY) : ?>
                                                                <a href="<?php echo base_url('company/view/' . $property['detail']['company_id']); ?>"
                                                                   target="_blank" class="p--3 pr--5"><?= htmlspecialchars($property['detail']['name']) ?></a>
                                                               <?php endif; ?>

                                                            <?php if (@$property['type'] == EntityTypes::CONTACT) : ?>
                                                                <a href="<?php echo base_url('contact/view/' . $property['detail']['contact_id']); ?>"
                                                                   target="_blank" class="p--3 pr--5"><?= htmlspecialchars($property['detail']['name']) ?></a>
                                                               <?php endif; ?>                                            
                                                        </td>                                        
                                                        <td><?php echo htmlspecialchars($prop['store_number']); ?></td>
                                                        <td><?php echo htmlspecialchars($prop['address']); ?></td>
                                                        <td><?php echo htmlspecialchars($prop['city']); ?></td>
                                                        <td><?php echo htmlspecialchars($prop['state']); ?></td>                                    
                                                        <td><?php echo htmlspecialchars($prop['zip_code']); ?></td>     


                                                        <td><?php echo htmlspecialchars(@$property['detail']['email']); ?></td>                                       
                                                        <td>
                                                            <?php
                                                            if ((is_array(@$property['detail']['phones']))) {
                                                                $phones = [];
                                                                foreach ($property['detail']['phones'] as $phone) {
                                                                    $phones[] = $phone->phone;
                                                                }

                                                                echo (is_array($phones)) ? implode(', ', $phones) : '';
                                                            }
                                                            ?>
                                                        </td>                                        
                                                        <td><?php echo htmlspecialchars(@$property['detail']['addresses'][0]->address); ?></td>
                                                        <td><?php echo htmlspecialchars(@$property['detail']['addresses'][0]->city); ?></td>
                                                        <td><?php echo htmlspecialchars(@$property['detail']['addresses'][0]->state); ?></td>                                    
                                                        <td><?php echo htmlspecialchars(@$property['detail']['addresses'][0]->zip_code); ?></td>




                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="<?php echo base_url('property/edit/' . $prop['property_id']); ?>"
                                                                   class="btn btn-outline btn-primary btn-xs p--3 pr--5"><i
                                                                        class="fi fi-pencil pr-0 mr-0 ml-1"></i> Edit</a>

                                                                <a href="<?php echo base_url('property/remove/' . $prop['property_id']); ?>"
                                                                   class="btn btn-outline-danger waves-effect waves-themed btn-xs p--3 pr--5 mr-1"> <i
                                                                        class="fi fi-close pr-0 mr-0 ml-1"></i> Delete</a>

                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
<?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="navs-top-owner">
                                <div class="card-body">                                    
                                     <table class="table w-100 card-table nowrap owners-datatable" data-ordering="false">
                                        <thead style="width:200% !important">
                                            <tr class="bg-lighter">
                                                <th>#</th>                                      
                                                <th>Owner Type</th>
                                                <th>Owner</th>   
                                                <th>Company</th>   
                                                <th style="display:none">Lead Gen Type</th>
                                                <th>Phone(s)</th>
                                                <th>Street Address</th>
                                                <th>City</th>
                                                <th>State</th>
                                                <th>Zip Code</th>  
                                            </tr>
                                        </thead>  
                                         <tbody>
                                            <?php $offset = 0;
foreach ($properties as $prop) : if ((@$prop['type'] == EntityTypes::CONTACT) || (@$prop['type'] == EntityTypes::COMPANY)): ?>  
                                                    <tr>
                                                        <td><?php echo @$offset += 1; ?></td>                                       
                                                        <td><?php echo htmlspecialchars(@$prop['type']); ?></td>  
                                                        <td>
                                                               <?php if (@$prop['type'] == EntityTypes::COMPANY) : ?>
                                                                <a href="<?php echo base_url('company/view/' . $prop['detail']['company_id']); ?>"
                                                                   target="_blank" class="p--3 pr--5"><?= htmlspecialchars($prop['detail']['name']) ?></a>
                                                            <?php endif; ?>

                                                               <?php if (@$prop['type'] == EntityTypes::CONTACT) : ?>
                                                                <a href="<?php echo base_url('contact/view/' . $prop['detail']['contact_id']); ?>"
                                                                   target="_blank" class="p--3 pr--5"><?= htmlspecialchars($prop['detail']['name']) ?></a>
        <?php endif; ?>

                                                        </td>
                                                        <td>
                                                               <?php if (@$prop['type'] == EntityTypes::CONTACT) : ?>
                                                                <a href="<?php echo base_url('company/view/' . $prop['detail']['company_id']); ?>"
                                                                   target="_blank" class="p--3 pr--5"><?= htmlspecialchars($prop['detail']['company_name']) ?></a>
        <?php endif; ?>

                                                        </td>
                                                       
                                                        <td style="display:none"><?php echo htmlspecialchars(@$prop['detail']['lead_gen_type']); ?></td>
                                                        <td>
                                                            <?php
                                                            if ((@$prop['type'] == EntityTypes::CONTACT) && (is_array(@$prop['detail']['phones']))) {
                                                                $phones = [];
                                                                foreach ($prop['detail']['phones'] as $phone) {
                                                                    $phones[] = $phone->phone;
                                                                }

                                                                echo (is_array($phones)) ? implode(', ', $phones) : '';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars(@$prop['detail']['addresses'][0]->address); ?></td>
                                                        <td><?php echo htmlspecialchars(@$prop['detail']['addresses'][0]->city); ?></td>
                                                        <td><?php echo htmlspecialchars(@$prop['detail']['addresses'][0]->state); ?></td>                                    
                                                        <td><?php echo htmlspecialchars(@$prop['detail']['addresses'][0]->zip_code); ?></td>   
                                                        
                                                    </tr>
    <?php endif;
endforeach; ?>
                                        </tbody>
                                    </table>
                                       
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
            </div>
            <!-- /Content -->
        </div>
    </div>
</div>

<style type="text/css">
    .dataTables_scrollHeadInner{
        width:100%;
    }
     div#DataTables_Table_0_wrapper {
        width: 100% !important;
    }
</style>


<script type="text/javascript">
    $(function () {
        $('.search-datatable').dataTable({
            dom: 'Bfrtip',
            pageLength: 100,
            "scrollY": 350,
            "scrollX": true,
            buttons: [
                {extend: 'excel', className: 'btn btn-primary fe-icon fe-excel ml-0'},
            ],
        });
        
        $('.owners-datatable').dataTable({
            dom: 'Bfrtip',
            buttons: [
                {extend: 'excel', className: 'btn btn-primary fe-icon fe-excel ml-0'},
            ],
        });

        $('.select2-states')
                .wrap('<div class="position-relative"></div>')
                .select2({
                    placeholder: 'Select States',
                    multiple: true,
                    ajax: {
                        url: '<?php echo base_url("property/states") ?>',
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

