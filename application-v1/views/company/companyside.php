<div class="card mb-4">
    <div class="card-body">
        <div class="media">
            <div class="media-body pt-2 ml-3">
                <h4 class="mb-2"><i class="ion ion-md-business mr-2" ></i> <?= $pageTitle ?>
                <a href="<?php echo base_url('company/edit/' . $company['company_id']); ?>"
                                                                   class="btn btn-outline btn-primary btn-xs p--3 pr--5"><i
                                                                        class="fi fi-pencil pr-0 mr-0 ml-1"></i> Edit</a>
                </h4>
                <div class="text-muted ">Company Details</div>
                 
            </div>
        </div>
    </div>
    <hr class="border-light m-0">
    <div class="card-body">
        <div class="mb-2">
            <span class="text-muted text-big">Name:</span>&nbsp;
            <?= htmlspecialchars(@$company['name']); ?> 
        </div>

        <?php foreach ($company['addresses'] as $address): ?> 
            <div class="mb-2">
                <span class="text-muted">Street Address:</span>&nbsp;
                <?= htmlspecialchars($address->address); ?> 
            </div>
            <div class="mb-2">
                <span class="text-muted">City:</span>&nbsp;
                <?= htmlspecialchars($address->city); ?> 
            </div>
            <div class="mb-2">
                <span class="text-muted">State:</span>&nbsp;
                <?= htmlspecialchars($address->state); ?> 
            </div>            
            <div class="mb-4">
                <span class="text-muted">Zip Code:</span>&nbsp;
                <?= htmlspecialchars($address->zip_code); ?> 
            </div>            
        <?php endforeach; ?> 

        <div class="mb-4">
            <span class="text-muted">Phone #:</span>&nbsp;                
            <?php
            foreach ($company['phones'] as $phone) {
                echo htmlspecialchars("($phone->phone) ");
            }
            ?> 

        </div>    


    </div>
</div>

