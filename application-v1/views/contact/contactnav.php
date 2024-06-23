<!-- Navigation -->
<div class="row no-gutters row-bordered  text-center mb-4">
    <ul class="nav nav-tabs tabs-alt nav-responsive-xl">
        <li class="nav-item">
                    <a class="nav-link <?php echo ($activeTab == "property") ? 'active' : '' ?>" href="<?= base_url("contact/view/" . $contact['contact_id'] . "?tab=property"); ?>"><i class="ion ion-md-business mr-2" ></i>Properties <span class="badge badge-outline-success">
                            <?= sizeof($contact['properties']) ?>
                </span></a>
        </li>                   
    </ul>

</div>
<!-- / Navigation -->