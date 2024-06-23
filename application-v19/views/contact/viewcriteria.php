<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row mb-3">
        <div class="col-lg-10">
            <a href="javascript:history.go(-1)" class="pb-5 text-secondary"><i class="ion ion-ios-arrow-back mr-2"></i> Back </a>
        </div>


    </div>
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">

            <!-- Content -->
            <div class="card w-100 mb-4 ">
                <div class="card-header with-elements">
                    <h5 class="card-header-title m-0 col-lg-10"><?php echo $pageTitle; ?>
                        <a href="<?php echo base_url('contact/edit_criteria/' . $criteria->criteria_id . "?page-type=contact"); ?>" class="btn btn-outline btn-primary btn-xs p--3 pr--5"><i class="fi fi-pencil pr-0 mr-0 ml-1"></i> Edit</a>
                        <a href="<?php echo base_url('contact/delete_criteria/' . $criteria->criteria_id . "?page-type=contact"); ?>" name="search-criteria" class="btn btn-outline btn-outline-danger btn-xs delete-button-confirm p--3 pr--5 mb-"><i class="fi fi-pencil pr-0 mr-0 ml-1"></i> Delete</a>
                    </h5>
                </div>

                <div class="card-body col-lg-12">
                    <table>
                        <tr>
                            <td style="width:250px"><strong>Contact Name: </strong> </td>
                            <td><?= htmlspecialchars($criteria->first_name . " " . $criteria->last_name) ?></td>
                        </tr>  
                        <tr>
                            <td style="width:250px"><strong>Availability Status: </strong> </td>
                            <td><?= htmlspecialchars($criteria->availability_status) ?></td>
                        </tr>  
                        <tr>
                            <td style="width:250px"><strong>Buyer Status: </strong> </td>
                            <td><?= htmlspecialchars($criteria->buyer_status) ?></td>
                        </tr> 
                        <tr>
                            <td style="width:250px"><strong>Property Type: </strong> </td>
                            <td><?= htmlspecialchars($criteria->property_type) ?></td>
                        </tr>    
                        <tr>
                            <td style="width:250px"><strong>Min Price: </strong> </td>
                            <td><?= htmlspecialchars("$" . number_format($criteria->min_asking_price)); ?></td>
                        </tr> 
                        <tr>
                            <td style="width:250px"><strong>Max Price: </strong> </td>
                            <td><?= htmlspecialchars( "$" .  number_format($criteria->max_asking_price)); ?></td>
                        </tr>  
                        <tr>
                            <td style="width:250px"><strong>Min Cap Rate: </strong> </td>
                            <td><?= htmlspecialchars($criteria->min_asking_rate . "%"); ?></td>
                        </tr>                       
                        <tr>
                            <td style="width:250px"><strong>Minimum Lease Term Remaining: </strong> </td>
                            <td><?= htmlspecialchars($criteria->lease_term_remaining); ?></td>
                        </tr> 
                        <tr>
                            <td style="width:250px"><strong>Landlord Responsibilities: </strong> </td>
                            <td><?= htmlspecialchars($criteria->landlord_responsibilities); ?></td>
                        </tr> 
                        <tr>
                            <td style="width:250px"><strong>Tenant Name: </strong> </td>
                            <td><?= htmlspecialchars($criteria->tenant_name); ?></td>
                        </tr>                    
                        <tr>
                            <td style="width:250px"><strong>States: </strong> </td>
                            <td><?= htmlspecialchars($criteria->states); ?></td>
                        </tr> 
                        <tr>
                            <td style="width:250px"><strong>Acquisition Criteria Update Date: </strong> </td>
                            <td><?= formatDate($criteria->criteria_update_date); ?></td>
                        </tr> 
                        <tr>
                            <td style="width:250px"><strong>Comments: </strong> </td>
                            <td><?= htmlspecialchars($criteria->comment); ?></td>
                        </tr> 
                    </table>                    
                    <br><br>
                    </p>
                </div>
            </div>
            <!-- /Content -->
        </div>
    </div>
</div>