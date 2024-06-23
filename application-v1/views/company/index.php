<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">

            <!-- Content -->
            <div class="card w-100 mb-1">
                <div class="card-body">
                    <?php echo form_open(base_url("company/"), array('method' => 'post','enctype' => 'multipart/form-data')) ?>
                        <div class="form-group">
                            <label class="form-label w-100">Company Upload File</label>
                            <input type="file" name="file">
                            <small class="form-text text-muted">Allowed files: .xls/xlsx only.</small>
                            <span class="text-danger"><?php echo @$uploadError; ?></span>
                            <a target='_blank' href="<?= base_url("/uploads/company-upload-temp.xlsx") ?>" class='mt-2'><i class='ios ion-ios-download '></i> download upload template</a>
                        </div>
                        <button type="submit" class="btn btn-default">
                            <i class='ion ion-ios-cloud-upload'></i>
                            Upload Companies</button>
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
                    <?php echo form_open(base_url("company/"), ['method' => 'get']); ?>
                    <div class="form-group col-12 mb-0 ">
                        <div class="input-group mb-1">
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$_GET['name']); ?>" name="name" placeholder="search by company name">
                            <input type="hidden" value="1" name="filter" />
                            <span class="input-group-append">
                                <button class="btn btn-default advance-search" type="button" data-toggle="collapse" href="#accordion-1" aria-expanded="true">Advanced</button>
                                <button class="btn btn-secondary" type="submit">Search</button>
                            </span>
                        </div>
                        <div id="accordion">
                            <div class="card mb-2 bg-transparent">
                                <div id="accordion-1" class="collapse" data-parent="#accordion" >
                                    <div class="card-body">

                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label class="form-label">Phone</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$_GET['phone']); ?>" placeholder="Phone" name="phone">
                                            </div>
                                            <div class="form-group col-md-8">
                                                <label class="form-label">Street Address</label>
                                                <input type="text" class="form-control" placeholder="Street Address" value="<?php echo htmlspecialchars(@$_GET['street_address']); ?>" name="street_address">
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label class="form-label">City</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$_GET['city']); ?>" placeholder="City" name="city">
                                            </div>
                                            <div class="form-group col-md-6 ">
                                                <label class="form-label">State: <?php echo @@$_GET['state']; ?></label>
                                                <select data-allow-clear="true" name="state" class="select2-states" style="width:100%"></select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="form-label">Zip Code</label>
                                                <input type="text" class="form-control" placeholder="Zip Code" value="<?php echo htmlspecialchars(@$_GET['zip_code']); ?>" name="zip_code">
                                            </div>
                                        </div>

                                        
                                        <div class="form-row">
                                            <div class="mt-2 col-lg-12">
                                                <button type="submit" class="btn btn-primary mt-3">Search</button>
                                                <a style="margin-left:5px;" href="<?php echo base_url('company/') ?>" class="btn btn-outline-secondary mt-3">Clear Options</a>
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
                    <h5 class="card-header-title mr-2 m-0"><?php echo $pageTitle; ?></h5>
                    <div class="card-header-elements ml-md-auto">
                        <a href="<?php echo base_url("company/add") ?>" class="btn btn-xs btn-outline-primary">
                            <span class="ion ion-md-add"></span> Add Company</a>
                    </div>
                </div>
                <div class="card-body" style="overflow-x: scroll; padding-right:10px;">
                    <table class="table m-0 card-table nowrap search-datatable mt-3" style='white-space:nowrap;'>
                        <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th style='width:25%'>Street</th>
                            <th>City</th>
                            <th>State</th>                            
                            <th>Zip Code</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($companies as $company) { ?>
                            <tr>
                                <td><?php echo @$offset += 1; ?></td>
                                <td><a href='<?= base_url("company/view/" . @$company['company_id']) ?>'><?php echo htmlspecialchars($company['name']); ?></a></td>
                                <td><?php echo htmlspecialchars(@$company['addresses'][0]->address); ?></td>
                                <td><?php echo htmlspecialchars(@$company['addresses'][0]->city); ?></td>
                                <td><?php echo htmlspecialchars(@$company['addresses'][0]->state); ?></td>
                                <td><?php echo htmlspecialchars(@$company['addresses'][0]->zip_code); ?></td>
                                <td><?php echo htmlspecialchars(@$company['phones'][0]->phone); ?></td>
                                <td>
                                    <a href="<?php echo base_url('company/edit/' . $company['company_id']); ?>"
                                       class="btn btn-outline btn-primary btn-xs p--3 pr--5"><i
                                                class="fi fi-pencil pr-0 mr-0 ml-1"></i> Edit</a>
                                    <a href="<?php echo base_url('company/remove/' . $company['company_id']); ?>"
                                       class="btn btn-outline-danger waves-effect waves-themed btn-xs p--3 pr--5 mr-1"> <i
                                                class="fi fi-close pr-0 mr-0 ml-1"></i> Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <!--<div class="mt-5"><?php echo $pagination->create_links() ?></div>-->

                </div>
            </div>
            <!-- /Content -->
        </div>
    </div>
</div>


<script type="text/javascript">
    $(function () {
        $('.search-datatable').dataTable({
            dom: 'Bfrtip',
            pageLength: 100,
            "scrollY": 350,
            "scrollX": true,
            searching: false,
            paging: false,
            buttons: [
                 { extend: 'excel', className: 'btn btn-primary fe-icon fe-excel ml-0' },
            ],
        });
        
         $('.select2-states')
                .wrap('<div class="position-relative"></div>')
                .select2({
                    placeholder: 'Select State',
                    multiple: false,
                    ajax: {
                        url: '<?php echo base_url("company/states") ?>',
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