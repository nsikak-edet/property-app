<!DOCTYPE html>
<html lang="en" class="light-style layout-fixed">
    <head>
        <?php $user = $this->session->userdata('user'); ?>
        <title>Tri-Oak Properties / <?php echo $pageTitle; ?></title>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
        <meta name="description" content="">
        <meta name="viewport"
              content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900"
              rel="stylesheet">
        <!-- Icon fonts -->
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/fonts/fontawesome.css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/fonts/ionicons.css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/fonts/linearicons.css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/fonts/open-iconic.css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/fonts/pe-icon-7-stroke.css">

        <!-- Core stylesheets -->
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/css/rtl/bootstrap<?php $ci = & get_instance();
        echo ($ci->session->userdata('mode') != null) ? "-dark" : "" ?>.css"
              class="theme-settings-bootstrap-css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/css/rtl/appwork<?php echo ($ci->session->userdata('mode') != null) ? "-dark" : "" ?>.css"
              class="theme-settings-appwork-css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/css/rtl/theme-corporate<?php echo ($ci->session->userdata('mode') != null) ? "-dark" : "" ?>.css"
              class="theme-settings-theme-css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/css/rtl/colors<?php echo ($ci->session->userdata('mode') != null) ? "-dark" : "" ?>.css"
              class="theme-settings-colors-css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/css/rtl/uikit.css">

        <!--        <link rel="stylesheet" href="--><?php //echo base_url("assets/adminv2/assets/")  ?><!--css/demo.css">-->
        <link rel="stylesheet"
              href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/perfect-scrollbar/perfect-scrollbar.css">

        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/bootstrap-markdown/bootstrap-markdown.css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/quill/typography.css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/smart-sticky/dist/css/jquery.smartSticky.min.css">


        <!-- Load polyfills -->
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/js/polyfills.js"></script>
        <script>document['documentMode'] === 10 && document.write('<script src="https://polyfill.io/v3/polyfill.min.js?features=Intl.~locale.en"><\/script>')</script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/js/material-ripple.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/js/layout-helpers.js"></script>

        <!-- Theme settings -->
        <!-- This file MUST be included after core stylesheets and layout-helpers.js in the <head> section -->
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/js/theme-settings.js"></script>

        <!-- Core scripts -->
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/js/pace.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <!-- Libs -->
        <!--<link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/bootstrap-select/bootstrap-select.css">-->
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/select2/select2.css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.css">
        <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/datatables/datatables.css">


    </head>
    <body>
        <div class="page-loader">
            <div class="bg-primary"></div>
        </div>
        <!-- Layout wrapper -->
        <div class="layout-wrapper layout-2">
            <div class="layout-inner">
                <!-- Layout sidenav -->
                <div id="layout-sidenav" class="layout-sidenav sidenav sidenav-vertical bg-dark">


                    <!-- Brand demo (see assets/css/demo/demo.css) -->
                    <div class="app-brand demo">
                        <span class="app-brand-logo demo bg-primary pt-5">
                            <svg viewBox="0 0 148 80" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><linearGradient id="a" x1="46.49" x2="62.46" y1="53.39" y2="48.2" gradientUnits="userSpaceOnUse"><stop stop-opacity=".25" offset="0"></stop><stop stop-opacity=".1" offset=".3"></stop><stop stop-opacity="0" offset=".9"></stop></linearGradient><linearGradient id="e" x1="76.9" x2="92.64" y1="26.38" y2="31.49" xlink:href="#a"></linearGradient><linearGradient id="d" x1="107.12" x2="122.74" y1="53.41" y2="48.33" xlink:href="#a"></linearGradient></defs><path style="fill: #fff;" transform="translate(-.1)" d="M121.36,0,104.42,45.08,88.71,3.28A5.09,5.09,0,0,0,83.93,0H64.27A5.09,5.09,0,0,0,59.5,3.28L43.79,45.08,26.85,0H.1L29.43,76.74A5.09,5.09,0,0,0,34.19,80H53.39a5.09,5.09,0,0,0,4.77-3.26L74.1,35l16,41.74A5.09,5.09,0,0,0,94.82,80h18.95a5.09,5.09,0,0,0,4.76-3.24L148.1,0Z"></path><path transform="translate(-.1)" d="M52.19,22.73l-8.4,22.35L56.51,78.94a5,5,0,0,0,1.64-2.19l7.34-19.2Z" fill="url(#a)"></path><path transform="translate(-.1)" d="M95.73,22l-7-18.69a5,5,0,0,0-1.64-2.21L74.1,35l8.33,21.79Z" fill="url(#e)"></path><path transform="translate(-.1)" d="M112.73,23l-8.31,22.12,12.66,33.7a5,5,0,0,0,1.45-2l7.3-18.93Z" fill="url(#d)"></path></svg>
                        </span>
                        <span  class="app-brand-text demo sidenav-text font-weight-normal ml-2">Tri-Oak Properties</span>
                        <a href="javascript:void(0)" class="layout-sidenav-toggle sidenav-link text-large ml-auto"                  
                           title="Expand/Collapse">
                            <i class="ion ion-md-menu align-middle"></i>
                        </a>
                    </div>

                    <div class="sidenav-divider mt-0"></div>
                    <!-- Links -->
                    <ul class="sidenav-inner py-1">
                        <li class="sidenav-header small font-weight-semibold">MENU</li>
                        <!-- UI elements -->
<?php if ($this->authenticate->isAdmin() || $this->authenticate->isSuperAdmin()): ?>
                            <li class="sidenav-item <?php echo openSideNav('user'); ?>">
                                <a href="javascript:void(0)" class="sidenav-link sidenav-toggle"><i
                                        class="sidenav-icon ion ion-ios-people"></i>
                                    <div>Users</div>
                                </a>
                                <ul class="sidenav-menu">
                                    <li class="sidenav-item <?php echo openSideNav('user'); ?>">
                                        <a href="<?php echo base_url("user/all") ?>" class="sidenav-link">
                                            <div>All Users</div>
                                        </a>
                                    </li>                            
                                </ul>
                            </li>

                            <li class="sidenav-item <?php echo openSideNav('company'); ?>">
                                <a href="<?php echo base_url("company/") ?>" class="sidenav-link"><i
                                        class="sidenav-icon ion ion-ios-business"></i>
                                    <div>Companies</div>
                                </a>
                            </li>

                            <li class="sidenav-item <?php echo openSideNav('contact'); ?>">
                                <a href="<?php echo base_url("contact/") ?>" class="sidenav-link"><i
                                        class="sidenav-icon ion ion-ios-contacts"></i>
                                    <div>Contacts</div>
                                </a>
                            </li>

                            <li class="sidenav-item <?php echo openSideNav('property'); ?>">
                                <a href="<?php echo base_url("property/") ?>" class="sidenav-link"><i
                                        class="sidenav-icon ion ion-ios-home"></i>
                                    <div>Properties</div>
                                </a>
                            </li>
                            
                            <li class="sidenav-item <?php echo openSideNav('duplicate'); ?>">
                                <a href="javascript:void(0)" class="sidenav-link sidenav-toggle"><i
                                        class="sidenav-icon ion ion-ios-link"></i>
                                    <div>Duplicates</div>
                                </a>
                                <ul class="sidenav-menu">
                                    <li class="sidenav-item <?php echo openSideNav('duplicate'); ?>">
                                        <a href="<?php echo base_url("duplicate/company") ?>" class="sidenav-link">
                                            <div>Company</div>
                                        </a>
                                    </li>  
                                     <li class="sidenav-item">
                                        <a href="<?php echo base_url("duplicate/") ?>" class="sidenav-link">
                                            <div>Contact</div>
                                        </a>
                                    </li> 
                                </ul>
                            </li>
<?php endif; ?>

                        <li class="sidenav-divider mb-1"></li>
                    </ul>
                </div>
                <!-- / Layout sidenav -->
                <!-- Layout container -->
                <div class="layout-container">
                    <!-- Layout navbar -->
                    <nav class="layout-navbar navbar navbar-expand-lg bg-secondary align-items-lg-center container-p-x" id="layout-navbar">
                        <!-- Brand demo (see assets/css/demo/demo.css) -->
                        <a href="index.html" class="navbar-brand app-brand demo d-lg-none py-0 mr-4">
                            <span class="app-brand-logo demo bg-primary">
                                <svg viewBox="0 0 148 80" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><linearGradient
                                    id="a" x1="46.49" x2="62.46" y1="53.39" y2="48.2" gradientUnits="userSpaceOnUse"><stop
                                    stop-opacity=".25" offset="0"></stop><stop stop-opacity=".1" offset=".3"></stop><stop
                                    stop-opacity="0" offset=".9"></stop></linearGradient><linearGradient id="e" x1="76.9"
                                    x2="92.64"
                                    y1="26.38"
                                    y2="31.49"
                                    xlink:href="#a"></linearGradient><linearGradient
                                    id="d" x1="107.12" x2="122.74" y1="53.41" y2="48.33"
                                    xlink:href="#a"></linearGradient></defs><path style="fill: #fff;"
                                    transform="translate(-.1)"
                                    d="M121.36,0,104.42,45.08,88.71,3.28A5.09,5.09,0,0,0,83.93,0H64.27A5.09,5.09,0,0,0,59.5,3.28L43.79,45.08,26.85,0H.1L29.43,76.74A5.09,5.09,0,0,0,34.19,80H53.39a5.09,5.09,0,0,0,4.77-3.26L74.1,35l16,41.74A5.09,5.09,0,0,0,94.82,80h18.95a5.09,5.09,0,0,0,4.76-3.24L148.1,0Z"></path><path
                                    transform="translate(-.1)"
                                    d="M52.19,22.73l-8.4,22.35L56.51,78.94a5,5,0,0,0,1.64-2.19l7.34-19.2Z" fill="url(#a)"></path><path
                                    transform="translate(-.1)" d="M95.73,22l-7-18.69a5,5,0,0,0-1.64-2.21L74.1,35l8.33,21.79Z"
                                    fill="url(#e)"></path><path transform="translate(-.1)"
                                    d="M112.73,23l-8.31,22.12,12.66,33.7a5,5,0,0,0,1.45-2l7.3-18.93Z"
                                    fill="url(#d)"></path></svg>
                            </span>
                            <span class="app-brand-text demo font-weight-normal ml-2" style="cursor:default">TSN Portal</span>
                        </a>
                        <!-- Sidenav toggle (see assets/css/demo/demo.css) -->
                        <div class="layout-sidenav-toggle navbar-nav d-lg-none align-items-lg-center mr-auto">
                            <a class="nav-item nav-link px-0 mr-lg-4" href="javascript:void(0)"
                               title="Expand/Collapse">
                                <i class="ion ion-md-menu text-large align-middle"></i>
                            </a>
                        </div>
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                                data-target="#layout-navbar-collapse">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="navbar-collapse collapse" id="layout-navbar-collapse">
                            <!-- Divider -->
                            <hr class="d-lg-none w-100 my-2">
                            <div class="navbar-nav align-items-lg-center">
                                <!-- Search -->

                            </div>
                            <div class="navbar-nav align-items-lg-center ml-auto">
                                <!-- Divider -->
                                <div class="demo-navbar-user nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                                        <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
                                            <img src="<?php echo base_url("assets/adminv2/assets/") ?>img/avatars/1.png" alt
                                                 class="d-block ui-w-30 rounded-circle">
                                            <i class="ion ion-md-person d-block"></i>
                                            <span class="px-1 mr-lg-2 ml-2 ml-lg-0"><?php echo htmlspecialchars($user->first_name . " " . $user->last_name); ?></span>
                                        </span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="<?php echo base_url('user/edit/' . $user->user_id); ?>" class="dropdown-item"><i
                                                class="ion ion-md-settings text-lightest"></i> &nbsp; Account settings</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="<?php echo ($this->authenticate->isAdmin() || $this->authenticate->isSuperAdmin() || $this->authenticate->isReviewer()) ?
        base_url("user/logout") : base_url("user/logout");
?>" class="dropdown-item"><i
                                                class="ion ion-ios-log-out text-danger"></i> &nbsp; Log Out</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </nav>
                    <!-- / Layout navbar -->
                    <!-- Layout content -->
                    <div class="layout-content">
                        <!-- BEGIN App Notification Messages -->
                            <?php if ($this->session->flashdata("success") != null) : ?>
                            <div class="alert alert-primary alert-dismissible fade show ">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                            <?php echo $this->session->flashdata("success"); ?>
                            </div>
                        <?php endif; ?>

                            <?php if ($this->session->flashdata("info") != null) : ?>
                            <div class="alert alert-info alert-dismissible fade show ">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                            <?php echo $this->session->flashdata("info"); ?>
                            </div>
                        <?php endif; ?>

                            <?php if ($this->session->flashdata("error") != null) : ?>
                            <div class="alert alert-danger alert-dismissible fade show ">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                            <?php echo $this->session->flashdata("error"); ?>
                            </div>
<?php endif; ?>
                        <!-- END App Notification Messages -->

                        <div class="container-fluid flex-grow-1 container-p-y">
<?php echo $body; ?>
                            <!-- / Content -->
                        </div>
                        <!-- Layout footer -->
                        <nav class="layout-footer footer bg-footer-theme">
                            <div class="container-fluid d-flex flex-wrap justify-content-between text-center container-p-x pb-3">
                                <div class="pt-3">
                                    <span class="footer-text font-weight-bolder">Tri-Oak Properties </span> © <?= date('Y') ?>
                                </div>
                                <!--                        <div>
                                                            <a href="javascript:void(0)" class="footer-link pt-3 ml-4">Help</a>
                                                            <a href="javascript:void(0)" class="footer-link pt-3 ml-4">Terms &amp; Conditions</a>
                                                        </div>-->
                            </div>
                        </nav>
                        <!-- / Layout footer -->
                    </div>
                    <!-- Layout content -->
                </div>
                <!-- / Layout container -->
            </div>
            <!-- Overlay -->
            <div class="layout-overlay layout-sidenav-toggle"></div>
        </div>
        <!-- / Layout wrapper -->
        <!-- Core scripts -->
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/popper/popper.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/js/bootstrap.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/js/sidenav.js"></script>
        <!-- Libs -->
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/chartjs/chartjs.js"></script>
        <!-- Demo -->
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>js/demo.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>js/dashboards_dashboard-2.js"></script>

        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/markdown/markdown.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/bootstrap-markdown/bootstrap-markdown.js"></script>

        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/moment/moment.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>js/ui_tooltips.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/vanilla-text-mask/vanilla-text-mask.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/vanilla-text-mask/text-mask-addons.js"></script>

        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/bootstrap-select/bootstrap-select.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/select2/select2.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/bootbox/bootbox.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/clone-field-increment-id/cloneData.js"></script>

        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/datatables/datatables.js"></script>
        <script src="<?php echo base_url("assets/adminv2/assets/") ?>js/tables_datatables.js"></script>

<!--<script src="--><?php //echo base_url("assets/adminv2/assets/")  ?><!--vendor/libs/smart-sticky/dist/js/jquery.smartSticky.min.js"></script>-->
<!--<script src="--><?php //echo base_url("assets/adminv2/assets/")  ?><!--vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>-->
        <script type="text/javascript" src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/datatables/Buttons-1.5.6/js/dataTables.buttons.js"></script>
        <script type="text/javascript" src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/datatables/Buttons-1.5.6/js/buttons.flash.js"></script>
        <script type="text/javascript" src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/datatables/Buttons-1.5.6/js/buttons.html5.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>


    </body>
</html>



















