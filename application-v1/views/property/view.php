<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row mb-3">
        <div class="col-lg-12">
            <a href="javascript:history.go(-1)" class="pb-5 text-secondary"><i class="ion ion-ios-arrow-back mr-2" ></i> Back </a>
        </div>
    </div>
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">

            <!-- Content -->
            <div class="card w-100 mb-4 ">
                <div class="card-header with-elements">
                    <h5 class="card-header-title mr-2 m-0"><?php echo $pageTitle; ?>
                        <a href="<?php echo base_url('property/edit/' . $property['property_id']); ?>"
                                                                   class="btn btn-outline btn-primary btn-xs p--3 pr--5"><i
                                                                        class="fi fi-pencil pr-0 mr-0 ml-1"></i> Edit</a>
                    </h5>
                </div>
                <div class="card-body col-lg-9">
                    <p> 
                        <strong>Property Name: </strong> <?= htmlspecialchars($property['property_name']) ?><br>
                        <strong>Property Type: </strong> <?= htmlspecialchars($property['property_type']) ?><br>
                        <strong>Store #: </strong> <?= htmlspecialchars($property['store_number']) ?><br>
                        <strong>Google Map: </strong> <?= anchor($property['google_map_link'],'',['target'=>'_blank']) ?><br><br>
                        
                        <h3>Address</h3>
                        <strong>Street Address: </strong> <?= htmlspecialchars($property['address']) ?><br>
                        <strong>City: </strong> <?= htmlspecialchars($property['city']) ?><br>
                        <strong>State: </strong> <?= htmlspecialchars($property['state']) ?><br>
                        <strong>Zip Code: </strong> <?= htmlspecialchars($property['zip_code']) ?><br><br>
                        
                        <h3>Owner</h3>
                        <strong>Company: </strong> <?php echo (strlen($property['name']) > 0) ? anchor(base_url('company/view/' . $property['company_id']), $property['name'], 'class="link-class"') : '' ?> <br>
                        <strong>Contact: </strong> <?php echo (strlen($property['first_name'] . $property['last_name']) > 0) ? anchor(base_url('contact/view/' . $property['contact_id']), $property['first_name'] . ' '. $property['last_name'], 'class="link-class"') : '' ?><br>
                        <br>
                         
                    </p>           
                </div>
            </div>
            <!-- /Content -->
        </div>
    </div>
</div>
