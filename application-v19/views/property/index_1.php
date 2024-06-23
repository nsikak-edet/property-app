<div class="container-fluid flex-grow-1 container-p-y pb-0">
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">
            <!-- Content -->
            <div class="card w-100 mb-1">
                <div class="card-body">
                    <?php echo form_open(base_url("property/"), array('method' => 'post', 'enctype' => 'multipart/form-data')) ?>
                    <div class="form-group">
                        <label class="form-label w-100">Property Upload File</label>
                        <input type="file" name="file">
                        <small class="form-text text-muted">Allowed files: .xls/xlsx only.</small>
                        <span class="text-danger"><?php echo @$uploadError; ?></span>
                        <a target='_blank' href="<?= base_url("/uploads/property-upload-temp.xlsx") ?>" class='mt-2'><i class='ios ion-ios-download '></i> download upload template</a>
                    </div>
                    <button type="submit" class="btn btn-default">
                        <i class='ion ion-ios-cloud-upload'></i>
                        Upload Properties</button>
                    </form>
                </div>
            </div>
            <!-- /Content -->
        </div>
    </div>
</div>




<div class="container-fluid flex-grow-1 container-p-y">

            <h4 class="font-weight-bold py-3 mb-4">
              Account settings
            </h4>

            <div class="card overflow-hidden">
              <div class="row no-gutters row-bordered row-border-light">
                <div class="col-md-3 pt-0">
                  <div class="list-group list-group-flush account-settings-links">
                    <a class="list-group-item list-group-item-action active" data-toggle="list" href="#account-general">General</a>
                    <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-change-password">Change password</a>
                    <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-info">Info</a>
                    <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-social-links">Social links</a>
                    <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-connections">Connections</a>
                    <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-notifications">Notifications</a>
                  </div>
                </div>
                <div class="col-md-9">
                  <div class="tab-content">
                    <div class="tab-pane fade active show" id="account-general">

                      <div class="card-body media align-items-center">
                        <img src="/products/appwork/v152/assets_/img/avatars/5-small.png" alt="" class="d-block ui-w-80">
                        <div class="media-body ml-4">
                          <label class="btn btn-outline-primary">
                            Upload new photo
                            <input type="file" class="account-settings-fileinput">
                          </label> &nbsp;
                          <button type="button" class="btn btn-default md-btn-flat">Reset</button>

                          <div class="text-light small mt-1">Allowed JPG, GIF or PNG. Max size of 800K</div>
                        </div>
                      </div>
                      <hr class="border-light m-0">

                      <div class="card-body">
                        <div class="form-group">
                          <label class="form-label">Username</label>
                          <input type="text" class="form-control mb-1" value="nmaxwell">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Name</label>
                          <input type="text" class="form-control" value="Nelle Maxwell">
                        </div>
                        <div class="form-group">
                          <label class="form-label">E-mail</label>
                          <input type="text" class="form-control mb-1" value="nmaxwell@mail.com">
                          <div class="alert alert-warning mt-3">
                            Your email is not confirmed. Please check your inbox.<br>
                            <a href="javascript:void(0)">Resend confirmation</a>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="form-label">Company</label>
                          <input type="text" class="form-control" value="Company Ltd.">
                        </div>
                      </div>

                    </div>
                    <div class="tab-pane fade" id="account-change-password">
                      <div class="card-body pb-2">

                        <div class="form-group">
                          <label class="form-label">Current password</label>
                          <input type="password" class="form-control">
                        </div>

                        <div class="form-group">
                          <label class="form-label">New password</label>
                          <input type="password" class="form-control">
                        </div>

                        <div class="form-group">
                          <label class="form-label">Repeat new password</label>
                          <input type="password" class="form-control">
                        </div>

                      </div>
                    </div>
                    <div class="tab-pane fade" id="account-info">
                      <div class="card-body pb-2">

                        <div class="form-group">
                          <label class="form-label">Bio</label>
                          <textarea class="form-control" rows="5">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris nunc arcu, dignissim sit amet sollicitudin iaculis, vehicula id urna. Sed luctus urna nunc. Donec fermentum, magna sit amet rutrum pretium, turpis dolor molestie diam, ut lacinia diam risus eleifend sapien. Curabitur ac nibh nulla. Maecenas nec augue placerat, viverra tellus non, pulvinar risus.</textarea>
                        </div>
                        <div class="form-group">
                          <label class="form-label">Birthday</label>
                          <input type="text" class="form-control" value="May 3, 1995">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Country</label>
                          <select class="custom-select">
                            <option>USA</option>
                            <option selected="">Canada</option>
                            <option>UK</option>
                            <option>Germany</option>
                            <option>France</option>
                          </select>
                        </div>
                        <div class="form-group">
                          <label class="form-label">Languages</label>
                          <div class="position-relative"><select multiple="" class="account-settings-multiselect form-control w-100 select2-hidden-accessible" data-select2-id="1" tabindex="-1" aria-hidden="true">
                            <option selected="" data-select2-id="3">English</option>
                            <option>German</option>
                            <option>French</option>
                          </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="2" style="width: 100px;"><span class="selection"><span class="select2-selection select2-selection--multiple" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="-1" aria-disabled="false"><ul class="select2-selection__rendered"><li class="select2-selection__choice" title="English" data-select2-id="4"><span class="select2-selection__choice__remove" role="presentation">×</span>English</li><li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" placeholder="" style="width: 0.75em;"></li></ul></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span></div>
                        </div>

                      </div>
                      <hr class="border-light m-0">
                      <div class="card-body pb-2">

                        <h6 class="mb-4">Contacts</h6>
                        <div class="form-group">
                          <label class="form-label">Phone</label>
                          <input type="text" class="form-control" value="+0 (123) 456 7891">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Website</label>
                          <input type="text" class="form-control" value="">
                        </div>

                      </div>
                      <hr class="border-light m-0">
                      <div class="card-body pb-2">

                        <h6 class="mb-4">Interests</h6>
                        <div class="form-group">
                          <label class="form-label">Favorite music</label>
                          <div class="bootstrap-tagsinput"><div style="position:absolute;width:0;height:0;z-index:-100;opacity:0;overflow:hidden;"><div class="bootstrap-tagsinput-input" style="position:absolute;z-index:-101;top:-9999px;opacity:0;white-space:nowrap;"></div></div><span class="tag badge badge-default">Rock<span data-role="remove"></span></span> <span class="tag badge badge-default">Alternative<span data-role="remove"></span></span> <span class="tag badge badge-default">Electro<span data-role="remove"></span></span> <span class="tag badge badge-default">Drum &amp; Bass<span data-role="remove"></span></span> <span class="tag badge badge-default">Dance<span data-role="remove"></span></span> <input type="text" placeholder="" style="width: 12px;"></div><input type="text" class="form-control account-settings-tagsinput" value="Rock,Alternative,Electro,Drum &amp; Bass,Dance" style="display: none;">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Favorite movies</label>
                          <div class="bootstrap-tagsinput"><div style="position:absolute;width:0;height:0;z-index:-100;opacity:0;overflow:hidden;"><div class="bootstrap-tagsinput-input" style="position:absolute;z-index:-101;top:-9999px;opacity:0;white-space:nowrap;"></div></div><span class="tag badge badge-default">The Green Mile<span data-role="remove"></span></span> <span class="tag badge badge-default">Pulp Fiction<span data-role="remove"></span></span> <span class="tag badge badge-default">Back to the Future<span data-role="remove"></span></span> <span class="tag badge badge-default">WALL·E<span data-role="remove"></span></span> <span class="tag badge badge-default">Django Unchained<span data-role="remove"></span></span> <span class="tag badge badge-default">The Truman Show<span data-role="remove"></span></span> <span class="tag badge badge-default">Home Alone<span data-role="remove"></span></span> <span class="tag badge badge-default">Seven Pounds<span data-role="remove"></span></span> <input type="text" placeholder="" style="width: 12px;"></div><input type="text" class="form-control account-settings-tagsinput" value="The Green Mile,Pulp Fiction,Back to the Future,WALL·E,Django Unchained,The Truman Show,Home Alone,Seven Pounds" style="display: none;">
                        </div>

                      </div>
                    </div>
                    <div class="tab-pane fade" id="account-social-links">
                      <div class="card-body pb-2">

                        <div class="form-group">
                          <label class="form-label">Twitter</label>
                          <input type="text" class="form-control" value="https://twitter.com/user">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Facebook</label>
                          <input type="text" class="form-control" value="https://www.facebook.com/user">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Google+</label>
                          <input type="text" class="form-control" value="">
                        </div>
                        <div class="form-group">
                          <label class="form-label">LinkedIn</label>
                          <input type="text" class="form-control" value="">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Instagram</label>
                          <input type="text" class="form-control" value="https://www.instagram.com/user">
                        </div>

                      </div>
                    </div>
                    <div class="tab-pane fade" id="account-connections">
                      <div class="card-body">
                        <button type="button" class="btn btn-twitter">Connect to <strong>Twitter</strong></button>
                      </div>
                      <hr class="border-light m-0">
                      <div class="card-body">
                        <h5 class="mb-2">
                          <a href="javascript:void(0)" class="float-right text-muted text-tiny"><i class="ion ion-md-close"></i> Remove</a>
                          <i class="ion ion-logo-google text-google"></i>
                          You are connected to Google:
                        </h5>
                        nmaxwell@mail.com
                      </div>
                      <hr class="border-light m-0">
                      <div class="card-body">
                        <button type="button" class="btn btn-facebook">Connect to <strong>Facebook</strong></button>
                      </div>
                      <hr class="border-light m-0">
                      <div class="card-body">
                        <button type="button" class="btn btn-instagram">Connect to <strong>Instagram</strong></button>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="account-notifications">
                      <div class="card-body pb-2">

                        <h6 class="mb-4">Activity</h6>

                        <div class="form-group">
                          <label class="switcher">
                            <input type="checkbox" class="switcher-input" checked="">
                            <span class="switcher-indicator">
                              <span class="switcher-yes"></span>
                              <span class="switcher-no"></span>
                            </span>
                            <span class="switcher-label">Email me when someone comments on my article</span>
                          </label>
                        </div>
                        <div class="form-group">
                          <label class="switcher">
                            <input type="checkbox" class="switcher-input" checked="">
                            <span class="switcher-indicator">
                              <span class="switcher-yes"></span>
                              <span class="switcher-no"></span>
                            </span>
                            <span class="switcher-label">Email me when someone answers on my forum thread</span>
                          </label>
                        </div>
                        <div class="form-group">
                          <label class="switcher">
                            <input type="checkbox" class="switcher-input">
                            <span class="switcher-indicator">
                              <span class="switcher-yes"></span>
                              <span class="switcher-no"></span>
                            </span>
                            <span class="switcher-label">Email me when someone follows me</span>
                          </label>
                        </div>
                      </div>
                      <hr class="border-light m-0">
                      <div class="card-body pb-2">

                        <h6 class="mb-4">Application</h6>

                        <div class="form-group">
                          <label class="switcher">
                            <input type="checkbox" class="switcher-input" checked="">
                            <span class="switcher-indicator">
                              <span class="switcher-yes"></span>
                              <span class="switcher-no"></span>
                            </span>
                            <span class="switcher-label">News and announcements</span>
                          </label>
                        </div>
                        <div class="form-group">
                          <label class="switcher">
                            <input type="checkbox" class="switcher-input">
                            <span class="switcher-indicator">
                              <span class="switcher-yes"></span>
                              <span class="switcher-no"></span>
                            </span>
                            <span class="switcher-label">Weekly product updates</span>
                          </label>
                        </div>
                        <div class="form-group">
                          <label class="switcher">
                            <input type="checkbox" class="switcher-input" checked="">
                            <span class="switcher-indicator">
                              <span class="switcher-yes"></span>
                              <span class="switcher-no"></span>
                            </span>
                            <span class="switcher-label">Weekly blog digest</span>
                          </label>
                        </div>

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>























<div class="container-fluid flex-grow-1 container-p-y mt-0">
    <div class="row">
        <div class="d-flex col-xl-12 align-items-stretch">
            <!-- Content -->
            <div class="card w-100 mb-1">
                <div class="card-body">                    
                    <?php echo form_open(base_url("property/ajaxSearch/"), ['method' => 'post', 'id' => 'search-form']); ?>
                    <div class="form-group col-12 mb-0 ">

                        <div class="input-group mb-1">
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->name); ?>" name="name" placeholder="search property by name">
                            <span class="input-group-append">
                                <button class="btn btn-default advance-search" type="button" data-toggle="collapse" href="#accordion-1" aria-expanded="true">Advanced</button>
                                <button class="btn btn-secondary search-button" type="submit">Search</button>
                            </span>
                        </div>
                        <div id="accordion">
                            <div class="card mb-2 bg-transparent">
                                <div id="accordion-1" class="collapse <?php echo ($showAdvanceSearch) ? "show" : "" ?>" data-parent="#accordion" >
                                    <div class="card-body">

                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label class="form-label">Store #</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->store_no); ?>" placeholder="Store #" name="store_no">
                                            </div>
                                            <div class="form-group col-md-8">
                                                <label class="form-label">Street Address</label>
                                                <input type="text" class="form-control" placeholder="Street Address" value="<?php echo htmlspecialchars(@$search->street_address); ?>" name="street_address">
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label class="form-label">City</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->city); ?>" placeholder="City" name="city">
                                            </div>
                                            <div class="form-group col-md-6 ">

                                                <label class="form-label">State: <?php echo (is_array(@$search->state)) ? implode(', ', @$search->state) : ''; ?></label>
                                                <select data-allow-clear="true" name="state[]" class="select2-states" style="width:100%"></select>
<!--                                                <input type="text" class="form-control "  placeholder="for multiple, separate each sate by comma e.g state a, state b etc." value="" name="state">-->
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="form-label">Zip Code</label>
                                                <input type="text" class="form-control" placeholder="Zip Code" value="<?php echo htmlspecialchars(@$search->zip_code); ?>" name="zip_code">
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label class="form-label">(Contacts & Companies) who own "x" or "x-y" amount of properties</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->property_count); ?>" placeholder="Enter count e.g 1, 2, 3... and range e.g. 1-2,1-5 etc" name="property_count">
                                            </div>
                                        </div>

                                        <div class="form-row"> 
                                            <div class="form-group col-md-4">
                                                <label class="form-label">Lead Gen Type</label>
                                                <select class="form-control" value="<?php echo htmlspecialchars(@$search->contact_name); ?>" placeholder="Lead Gen Type" name="lead_gen_type">
                                                    <option value="">Select Type</option>
                                                    <option value="<?= LeadGenTypes::MET ?>" <?= (@$search->lead_gen_type == LeadGenTypes::MET) ? "selected='selected'" : "" ?>>Met or Haven't Met</option>
                                                    <option value="<?= LeadGenTypes::HAVENT_MET ?>" <?= (@$search->lead_gen_type == LeadGenTypes::HAVENT_MET) ? "selected='selected'" : "" ?>>Not a Met or Haven't Met</option>                                                    
                                                </select>

                                            </div>

                                            <div class="form-group col-md-4">
                                                <label class="form-label">Contact Name</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->first_name); ?>" placeholder="First Name" name="first_name">

                                                    <span class="input-group-append">
                                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->last_name); ?>" placeholder="Last Name" name="last_name">
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label class="form-label">Company</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(@$search->company); ?>" placeholder="Enter company name" name="company" >
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="mt-2 col-lg-12">
                                                <button type="submit" class="btn btn-primary mt-3 search-button">Search</button>
                                                <a style="margin-left:5px;" href="<?php echo base_url('property/reset_form') ?>" class="btn btn-outline-secondary mt-3">Clear Options</a>
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
                    <h5 class="card-header-title mr-2 m-0 badge badge-secondary p-1">Total Properties in Database(<?= tofloat($totalProperties) ?>)</h5>

                    <div class="card-header-elements ml-md-auto">
                        <a href="<?php echo base_url("property/add") ?>" class="btn btn-xs btn-outline-primary">
                            <span class="ion ion-md-add"></span> Add Property</a>
                    </div>
                </div>

                <div class="card-body"> 
                    
                     <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>
    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Profile</a></li>
    <li role="presentation"><a href="#contact" aria-controls="contact" role="tab" data-toggle="tab">Contact</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane fade in active" id="home">
      <p>Et et consectetur ipsum labore excepteur est proident excepteur ad velit occaecat qui minim occaecat veniam. Fugiat veniam incididunt anim aliqua enim pariatur veniam sunt est aute sit dolor anim. Velit non irure adipisicing aliqua ullamco irure incididunt irure non esse consectetur nostrud minim non minim occaecat. Amet duis do nisi duis veniam non est eiusmod tempor incididunt tempor dolor ipsum in qui sit. Exercitation mollit sit culpa nisi culpa non adipisicing reprehenderit do dolore. Duis reprehenderit occaecat anim ullamco ad duis occaecat ex.</p>
    </div>
    <div role="tabpanel" class="tab-pane fade" id="profile">
      <p>Nulla est ullamco ut irure incididunt nulla Lorem Lorem minim irure officia enim reprehenderit. Magna duis labore cillum sint adipisicing exercitation ipsum. Nostrud ut anim non exercitation velit laboris fugiat cupidatat. Commodo esse dolore fugiat sint velit ullamco magna consequat voluptate minim amet aliquip ipsum aute laboris nisi. Labore labore veniam irure irure ipsum pariatur mollit magna in cupidatat dolore magna irure esse tempor ad mollit. Dolore commodo nulla minim amet ipsum officia consectetur amet ullamco voluptate nisi commodo ea sit eu.</p>
    </div>
    <div role="tabpanel" class="tab-pane fade" id="contact">
      <p>Sint sit mollit irure quis est nostrud cillum consequat Lorem esse do quis dolor esse fugiat sunt do. Eu ex commodo veniam Lorem aliquip laborum occaecat qui Lorem esse mollit dolore anim cupidatat. Deserunt officia id Lorem nostrud aute id commodo elit eiusmod enim irure amet eiusmod qui reprehenderit nostrud tempor. Fugiat ipsum excepteur in aliqua non et quis aliquip ad irure in labore cillum elit enim. Consequat aliquip incididunt ipsum et minim laborum laborum laborum et cillum labore. Deserunt adipisicing cillum id nulla minim nostrud labore eiusmod et amet. Laboris consequat consequat commodo non ut non aliquip reprehenderit nulla anim occaecat. Sunt sit ullamco reprehenderit irure ea ullamco Lorem aute nostrud magna.</p>
    </div>
  </div>
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
   
                    <div id="accordion">
              <div class="card mb-2 ">
                <div class="card-header bg-lighter">
                  <a class="text-body" data-toggle="collapse" href="#accordion-1">
                    Properties
                  </a>
                </div>

                <div id="accordion-1" class="collapse show" >
                  <div class="card-body">
                    <table class="table nowrap card-table  search-result-datatable table-bordered" data-ordering="false">
                                        <thead>
                                            <tr class="bg-lighter">                                                
                                                <th>Property Name</th>                                 
<!--                                                <th>Lead Gen Type</th>-->
                                                <th>Property Type</th>
                                                <th>Owner Type</th>
                                                <th>Owner</th>                                
                                                <th>Store #</th>  
                                                <th>Street Address</th>
                                                <th>City</th>
                                                <th>State</th>
                                                <th>Zip Code</th>
                                                <th>Owner Email</th>     
                                                <th>Owner Phone #</th>      
                                                <th>Owner Street Address</th>      
                                                <th>Owner City</th>      
                                                <th>Owner State</th>      
                                                <th>Owner Zip Code</th>      
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                  </div>
                </div>
              </div>

              <div class="card mb-2">
                <div class="card-header bg-lighter">
                  <a class="collapsed text-body " data-toggle="collapse" href="#accordion-2">
                    Owners
                  </a>
                </div>
                <div id="accordion-2" class="collapse" >
                  <div class="card-body">
                    <table class="table card-table nowrap property-owners-datatable table-bordered" data-ordering="false">
                                        <thead>
                                            <tr class="bg-lighter">                                                                                     
                                                <th>Owner Type</th>
                                                <th>Owner</th>   
                                                <th>Company</th>   
<!--                                                <th style="display:none">Lead Gen Type</th>
-->                                             <th>Phone(s)</th>
                                                <th>Street Address</th>
                                                <th>City</th>
                                                <th>State</th>
                                                <th>Zip Code</th>  
                                            </tr>
                                        </thead>  
                                        <tbody>
                                           
                                        </tbody>
                                    </table>
                  </div>
                </div>
              </div>              
            </div>
                     
                    
             

                </div>

            </div>
            <!-- /Content -->
        </div>
    </div>
</div>


<style type="text/css">
    .dataTables_scrollHeadInner{
        width:100%;
    }
    div#DataTables_Table_0_wrapper {
        width: 100% !important;
    }
    div#DataTables_Table_0_wrapper {
        width: 100% !important;
    }
</style>


<script type="text/javascript">
    $(function () {


        $('.owners-datatable').dataTable({
            dom: 'Bfrtip',
            buttons: [
                {extend: 'excel', className: 'btn btn-primary fe-icon fe-excel ml-0'},
            ],
        });

        $('.search-button').on("click", function (e) {
            e.preventDefault();
            var formData = $('#search-form').serializeJSON();           

            //initialize property table
            $('.search-result-datatable').dataTable({
                dom: 'Bfrtip',
                processing: false,
                serverSide: false,
                ajax: {
                    type: 'POST',
                    url: "<?= base_url("property/ajaxSearch") ?>",
                    data: formData
                },
                columns: [
                    {data: "name"},
//                    {data: "lead_gen_type"},
                    {data: "property_type"},
                    {data: "type"},
                    {data: "owner"},
                    {data: "store_number"},
                    {data: "address"},
                    {data: "city"},
                    {data: "state"},
                    {data: "zip_code"},
                    {data: "email"},
                    {data: "phones"},
                    {data: "owner_address"},
                    {data: "owner_city"},
                    {data: "owner_state"},
                    {data: "owner_zip_code"},
                    {data: "action"}
                ],
                paging: false,
                "scrollY": 350,
                "scrollX": true,
                buttons: [
                    {extend: 'excel', className: 'btn btn-primary fe-icon fe-excel ml-0'},
                ],
                "bDestroy": true
            });
            
            //initialize owners table
            $('.property-owners-datatable').dataTable({
                dom: 'Bfrtip',
                processing: false,
                serverSide: false,
                ajax: {
                    type: 'POST',
                    url: "<?= base_url("property/ajaxSearch/0") ?>",
                    data: formData,
                    beforeSend: function() {                       
                        $('.search-button').text('searching...');
                        $('.search-button').prop('disabled',true);
                    },
                    complete: function() {
                        $('.search-button').text('Search');
                        $('.search-button').prop('disabled',false);
                    },
                },
                columns: [
                    {data: "type"},
                    {data: "owner"},
                    {data: "company"},
                    {data: "phones"},
                    {data: "owner_address"},
                    {data: "owner_city"},
                    {data: "owner_state"},
                    {data: "owner_zip_code"},
//                    {data: "action"}
                ],
                paging: false,
                "scrollY": 300,
                "scrollX": false,
                buttons: [
                    {extend: 'excel', className: 'btn btn-primary fe-icon fe-excel ml-0'},
                ],
                "bDestroy": true
            });
             $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
        });

        $('.select2-states')
                .wrap('<div class="position-relative"></div>')
                .select2({
                    placeholder: 'Select States',
                    multiple: true,
                    ajax: {
                        url: '<?php echo base_url("property/states") ?>',
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
                
                //initialize table with previous cached search criteria
        <?php if(isset($searchCriteria)): ?>
            $('.search-result-datatable').dataTable({
                    dom: 'Bfrtip',
                    processing: false,
                    serverSide: false,
                    ajax: {
                        type: 'GET',
                        url: "<?= base_url("property/ajaxSearch") ?>",
                        data: []
                    },
                    columns: [
                        {data: "name"},
    //                    {data: "lead_gen_type"},
                        {data: "property_type"},
                        {data: "type"},
                        {data: "owner"},
                        {data: "store_number"},
                        {data: "address"},
                        {data: "city"},
                        {data: "state"},
                        {data: "zip_code"},
                        {data: "email"},
                        {data: "phones"},
                        {data: "owner_address"},
                        {data: "owner_city"},
                        {data: "owner_state"},
                        {data: "owner_zip_code"},
                        {data: "action"}
                    ],
                    paging: false,
                    "scrollY": 300,
                    "scrollX": true,
                    buttons: [
                        {extend: 'excel', className: 'btn btn-primary fe-icon fe-excel ml-0'},
                    ],
                    "bDestroy": true
                });
                
                //initialize owners
                $('.property-owners-datatable').dataTable({
                dom: 'Bfrtip',
                processing: false,
                serverSide: false,
                ajax: {
                    type: 'GET',
                    url: "<?= base_url("property/ajaxSearch/0") ?>",
                    data: []
                },
                columns: [
                    {data: "type"},
                    {data: "owner"},
                    {data: "company"},
                    {data: "phones"},
                    {data: "owner_address"},
                    {data: "owner_city"},
                    {data: "owner_state"},
                    {data: "owner_zip_code"},
//                    {data: "action"}
                ],
                paging: false,
                scrollY:'50vh',
                "scrollX": true,
                buttons: [
                    {extend: 'excel', className: 'btn btn-primary fe-icon fe-excel ml-0'},
                ],
                "bDestroy": true
            });
                
          <?php endif; ?>
      
        $('a[data-toggle="accordion"]').on( 'shown.bs.tab', function (e) {
          $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
        });
    });
</script>

