<div class="card mb-4">
    <div class="card-header with-elements">
        <h5 class="card-header-title mr-2 m-0">Properties</h5>

    </div>
    <div class="card-datatable table-responsive">
        <table class="table table-striped datatable table-bordered" >
            <thead>
                <tr>
                    <th>#</th>
                    <th>Property Name</th>
                    <th>Property Type</th>
                    <th>Store #</th>  
                    <th>Street Address</th>
                     <th>City</th>
                    <th>State</th>                   
                    <th>Zip Code</th>                           

                </tr>
            </thead>
            <tbody>
                <?php foreach ($company['properties'] as $property) { ?>
                    <tr>
                        <td><?php echo @$offset += 1; ?></td>
                        <td><?php echo anchor(base_url('property/view/' . $property['property_id']), $property['name'], 'class="link-class"')
                                                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                        <td><?php echo htmlspecialchars($property['store_number']); ?></td>
                        <td><?php echo htmlspecialchars(@$property['address']); ?></td>
                         <td><?php echo htmlspecialchars(@$property['city']); ?></td>
                        <td><?php echo htmlspecialchars(@$property['state']); ?></td>                       
                        <td><?php echo htmlspecialchars(@$property['zip_code']); ?></td>                                

                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>