<div class="container-fluid flex-grow-1 container-p-y mt-0">
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">
            <!-- Content -->
            <div class="card w-100 mb-1">
                <div class="card-body">   
                    <?php echo form_open(base_url("duplicate/"), ['method' => 'post']); ?>
                    <div class="card mb-2 bg-transparent">
                        <div class="card-body">
                            <div class="form-row">                                            
                                <div class="form-group col-md-10 ">
                                    <label class="form-label">Fields to search for duplicate contacts (field match)</label>
                                    <select data-allow-clear="true" name="contact_duplicates[]" class="select2-fields" style="width:100%">  
                                        <option value="first_name">First Name</option>        
                                        <option value="last_name">Last Name</option>       
                                        <option value="email">Email</option>       
                                        <option value="address">Street</option>
                                        <option value="city">City</option>
                                        <option value="state">State</option>
                                        <option value="zip_code">Zip Code</option> 
                                        <option value="phone">Phone</option>  
                                        <option value="company">Company</option>       
                                    </select>
                                </div>
                                <div class="form-group col-md-2 ">                                    
                                    <button type="submit" class="btn btn-primary mt-4" name="duplicates" value="contact" type="button">Search Contact</button>
                                </div>
                            </div>      

                            <h3 class="h3 mt-3">Manual Search (value match)</h3>
                            
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="form-label">First Name: <span class="text-muted"><?= htmlspecialchars(@$jsonCriteria->first_name) ?></span></label>
                                    <select data-allow-clear="true" name="first_name" class="select2-field select2-field-first-name" style="width:100%">  
                                        <option value="empty">Empty</option> 
                                    </select>                                        
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Last Name: <span class="text-muted"><?= htmlspecialchars(@$jsonCriteria->last_name) ?></span></label>
                                    <select data-allow-clear="true" name="last_name" class="select2-field select2-field-last_name" style="width:100%">  
                                        <option value="empty">Empty</option> 
                                    </select>                                        
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Company: <span class="text-muted"><?= htmlspecialchars(@$jsonCriteria->company) ?></span></label>
                                    <select data-allow-clear="true" name="company" class="select2-field select2-field-company" style="width:100%">  
                                        <option value="empty">Empty</option> 
                                    </select>                                        
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="form-label">Phone: <span class="text-muted"><?= htmlspecialchars(@$jsonCriteria->phone) ?></span></label>
                                    <select data-allow-clear="true" name="phone" class="select2-field select2-field-phone" style="width:100%">  
                                        <option value="empty">Empty</option> 
                                    </select>                                        
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Email: <span class="text-muted"><?= htmlspecialchars(@$jsonCriteria->email) ?></span></label>
                                    <select data-allow-clear="true" name="email" class="select2-field select2-field-email" style="width:100%">  
                                        <option value="empty">Empty</option> 
                                    </select>                                        
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Street Address: <span class="text-muted"><?= htmlspecialchars(@$jsonCriteria->street_address) ?></span></label>
                                    <select data-allow-clear="true" name="street_address" class="select2-field select2-field-address" style="width:100%">  
                                        <option value="empty">Empty</option> 
                                    </select>                                        
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="form-label">City: <span class="text-muted"><?= htmlspecialchars(@$jsonCriteria->city) ?></span></label>
                                    <select data-allow-clear="true" name="city" class="select2-field select2-field-city" style="width:100%">  
                                        <option value="empty">Empty</option> 
                                    </select>                                        
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label class="form-label">State: <span class="text-muted"><?= htmlspecialchars(@$jsonCriteria->state) ?></span></label>
                                    <select data-allow-clear="true" name="state" class="select2-states" style="width:100%">
                                         <?php foreach($states as $option){
                                             echo "<option value='$option->state'>$option->state</option>";
                                         } ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="form-label">Zip Code: <span class="text-muted"><?= htmlspecialchars(@$jsonCriteria->zip_code) ?></span></label>
                                    <select data-allow-clear="true" name="zip_code" class="select2-field select2-field-zipcode" style="width:100%">  
                                        <option value="empty">Empty</option> 
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="mt-2 col-lg-12">
                                    <button type="submit" class="btn btn-primary mt-3">Search</button>
                                    <a style="margin-left:5px;" href="<?php echo base_url('duplicate/clear_contact_search') ?>" class="btn btn-outline-secondary mt-3">Clear Options</a>
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
                <div class="card-body table-responsive" style="overflow-x: scroll; padding-right:10px;">
                        <table class="table m-0 mt-2 nowrap card-table search-datatable" style='white-space:nowrap'>
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>First Name</th>                               
                                    <th>Last Name</th>
                                    <th>Company</th>
                                    <th>Lead Gen Type</th>
                                    <th style='width:25%'>Street</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Zip Code</th>
                                    <th>Phone</th>
                                    <th>Email</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contactDuplicates as $contact) { ?>
                                    <tr>
                                        <td><?php echo @$offset += 1; ?></td>
                                        <td><a href="<?= base_url("contact/view/" . $contact['contact_id']) ?>"><?php echo htmlspecialchars($contact['first_name']); ?></a>
                                            <span class="badge badge-sm badge-default"><?= htmlspecialchars($contact['contact_id']); ?></span>                                            
                                        </td>                                    
                                        <td><?php echo htmlspecialchars($contact['last_name']); ?></td>                                    
                                        <td><a href="<?= base_url("company/view/" . $contact['company_id']) ?>"><?= htmlspecialchars(@$contact['company_name']); ?></a></td>
                                        <td><?php echo htmlspecialchars($contact['lead_gen_type']); ?></td>
                                        <td><?php echo htmlspecialchars(@$contact['addresses']); ?></td>
                                        <td><?php echo htmlspecialchars(@$contact['city']); ?></td>
                                        <td><?php echo htmlspecialchars(@$contact['state']); ?></td>
                                        <td><?php echo htmlspecialchars(@$contact['zip_code']); ?></td>
                                        <td><?php echo htmlspecialchars(@$contact['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <h2  class="mt-5">Merge Contacts</h2>
                        <div class="form-row col-lg-7"> 
                            <?php echo form_open(base_url("duplicate/merge"), ['method' => 'post', 'style' => 'width:100%']); ?>
                            <div class="form-group col-md-12 ">
                                <label class="form-label ">Select contacts to merge</label>
                                <select data-allow-clear="true" name="merge_selections[]" class="select2-contacts" style="width:100%">                                                                            
                                    <?php
                                    foreach ($contactDuplicates as $con) {
                                        $contactId = $con['contact_id'];
                                        $contactName = $con['first_name'] . " [$contactId]";
                                        echo "<option value='$contactId|$contactName'>$contactName</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">Select parent record for the merge</label>
                                <select data-allow-clear="true" name="parent_record" class="select2-contact-parent" style="width:100%">                                                                                                            
                                </select>
                            </div>
                            <div class="col-md-4 ">                                    
                                <button type="submit" class="btn btn-primary mt-2" name="merge_type" value="contact" type="button">Merge Contacts </button>
                            </div>
                            </form>
                        </div>        

                    </div>
            </div>
            <!-- /Content -->
        </div>
    </div>
</div>


<style type="text/css">
    .position-relative{
        /*width:70%*/
    }
</style>

<script type="text/javascript">
    $(function () {
        $('.search-datatable').dataTable({
            dom: 'Bfrtip',
            searching: false,
            buttons: [
                {extend: 'excel', className: 'btn btn-primary fe-icon fe-excel ml-0'},
            ],
        });

        //parent records select2
        $('.select2-contact-parent, .select2-company-parent')
                .wrap('<div class="position-relative"></div>')
                .select2({
                    placeholder: 'Select Parent',
                    multiple: false,
                    tags: false
                }).val([]).trigger('change');

        $('.select2-contacts, .select2-companies')
                .wrap('<div class="position-relative"></div>')
                .select2({
                    placeholder: 'Select Fields',
                    multiple: true,
                    tags: false
                }).val([]).trigger('change');

        //duplicate select options
        $(document.body).on("change", ".select2-contacts, .select2-companies", function () {
            var selectedValue = $(this).val();
            $('.select2-contact-parent').html('').select2({data: [{id: '', text: ''}]});
            $('.select2-company-parent').html('').select2({data: [{id: '', text: ''}]});
            for (var i = 0; i < selectedValue.length; i++) {
                var optionName = selectedValue[i].split("|");
                var newOption = new Option(optionName[1], optionName[0], true, true);
                $('.select2-contact-parent').append(newOption).trigger('change');
                $('.select2-company-parent').append(newOption).trigger('change');
            }
            $('.select2-contact-parent').val(null).trigger('change');
            $('.select2-company-parent').val(null).trigger('change');
        });

        //states select options
        $('.select2-states')
                .wrap('<div class="position-relative"></div>')
                .select2({
                    placeholder: 'Select State (enter "empty" for empty record)',
                    multiple: false,
                    
                    tags: true
                }).val('').trigger('change');


        $('.select2-fields')
                .wrap('<div class="position-relative"></div>')
                .select2({
                    placeholder: 'Select Fields',
                    multiple: true,
                    tags: false
                }).val(<?= $jsonContactFields; ?>).trigger('change');

        $('.select2-contact-fields')
                .wrap('<div class="position-relative"></div>')
                .select2({
                    placeholder: 'Select Fields',
                    multiple: true,
                    tags: false
                }).val('').trigger('change');


        $('.select2-field')
                .wrap('<div class="position-relative"></div>')
                .select2({
                    placeholder: 'Enter Value (select "empty" for empty records)',
                    multiple: false,
                    tags: true
                }).val('').trigger('change');

    });
</script>