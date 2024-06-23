
<div class="card mb-4">    
    <div class="card-body">        
        <div class="media">            
            <div class="media-body pt-2 ml-3">
                <h4 class="mb-2"><i class="ion ion-ios-contact mr-2" ></i> <?= $pageTitle ?>                    
                <a href="<?php echo base_url('contact/edit/' . $contact['contact_id']); ?>"
                                                                   class="btn btn-outline btn-primary btn-xs p--3 pr--5"><i
                                                                        class="fi fi-pencil pr-0 mr-0 ml-1"></i> Edit</a>
                </h4>
            </div>
        </div>
    </div>
    <hr class="border-light m-0">
    <div class="card-body">        
        <div class="mb-2">
            <span class="text-muted text-big">First Name:</span>&nbsp;
            <?= htmlspecialchars(@$contact['first_name']); ?> 
        </div>        
        <div class="mb-2">
            <span class="text-muted text-big">Last Name:</span>&nbsp;
            <?= htmlspecialchars(@$contact['last_name']); ?> 
        </div>
        <div class="mb-2">
            <span class="text-muted text-big">Company:</span>&nbsp;
            <a href="<?= base_url("company/view/" . $contact['company_id']); ?>"><?= htmlspecialchars($contact['company_name']) ?></a>
        </div>
        <div class="mb-2">
            <span class="text-muted text-big">Lead Gen Type:</span>&nbsp;
            <?= htmlspecialchars(@$contact['lead_gen_type']); ?> 
        </div>       
        <?php foreach ($contact['addresses'] as $address): ?> 
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
            <div class="mb-1">
                <span class="text-muted">Zip Code:</span>&nbsp;
                <?= htmlspecialchars($address->zip_code); ?> 
            </div>            
        <?php endforeach; ?> 
        <div class="mb-1">
            <span class="text-muted">Phone #:</span>&nbsp;                
            <?php
            foreach ($contact['phones'] as $phone) {
                echo htmlspecialchars($phone->phone);
            }
            ?> 
        </div>  
         <div class="mb-2">
            <span class="text-muted text-big">Email:</span>&nbsp;
            <?= htmlspecialchars(@$contact['email']); ?> 
        </div>
    </div>
</div>

