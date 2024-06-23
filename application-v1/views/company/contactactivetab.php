<div class="card mb-4">
    <div class="card-header with-elements">
        <h5 class="card-header-title mr-2 m-0">Contacts</h5>

    </div>
    <div class="card-datatable table-responsive">
        <table class="table table-striped datatable table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>First Name</th>                   
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Street</th>
                    <th>City</th>
                    <th>State</th>                    
                    <th>Zip Code</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($company['contacts'] as $property) { ?>
                    <tr>
                        <td><?php echo @$offset += 1; ?></td>
                        <td><a href="<?= base_url("contact/view/" . $property['contact_id']); ?>"><?php echo htmlspecialchars($property['first_name']); ?></a></td>                        
                        <td><?php echo htmlspecialchars($property['last_name']); ?></td>
                         <td><?php echo htmlspecialchars($property['email']); ?></td>
                        <td><?php echo htmlspecialchars($property['company_name']); ?></td>
                        <td><?php echo htmlspecialchars(@$property['addresses'][0]->address); ?></td>
                        <td><?php echo htmlspecialchars(@$property['addresses'][0]->city); ?></td>
                        <td><?php echo htmlspecialchars(@$property['addresses'][0]->state); ?></td>                        
                        <td><?php echo htmlspecialchars(@$property['addresses'][0]->zip_code); ?></td>
                        <td><?php echo htmlspecialchars(@$property['phones'][0]->phone); ?></td>                                
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
</div>