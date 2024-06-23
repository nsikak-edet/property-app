<!-- Navigation -->
<div class="row no-gutters row-bordered  text-center mb-4">
    <ul class="nav nav-tabs tabs-alt nav-responsive-xl">
        <li class="nav-item">
            <a class="nav-link <?php echo ($activeTab == "contact") ? 'active' : '' ?>" href="<?= base_url("company/view/" . $company['company_id'] . "?tab=contact"); ?>"><i class="ion ion-ios-contacts mr-2" ></i>Contacts <span class="badge badge-primary"><?php echo sizeof(@$company['contacts']) ?></span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($activeTab == "property") ? 'active' : '' ?>" href="<?= base_url("company/view/" . $company['company_id'] . "?tab=property"); ?>" <?= (@$isViewOwnerPage) ? "target='_blank'" : ""; ?>><i class="ion ion-md-business mr-2" ></i>Properties <span class="badge badge-outline-success"><?= sizeof($company['properties']) ?></span></a>
        </li>   
        <?php if(sizeof(@$company['contacts']) > 0): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo ($activeTab == "search-criteria") ? 'active' : '' ?>" href="<?= base_url("contact/acquisition_criteria/" . @$company['contacts'][0]['contact_id'] . "?tab=search-criteria&page-type=company"); ?>"><i class="ion ion-md-business mr-2"></i> Acquisition Criteria
            </a>
        </li>  
        <?php endif; ?>              
    </ul>

</div>
<!-- / Navigation -->
