<!DOCTYPE html>
<html lang="en" class="light-style">
<head>
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
    <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/css/rtl/bootstrap.css"
          class="theme-settings-bootstrap-css">
    <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/css/rtl/appwork.css"
          class="theme-settings-appwork-css">
    <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/css/rtl/theme-corporate.css"
          class="theme-settings-theme-css">
    <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/css/rtl/colors.css"
          class="theme-settings-colors-css">
    <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/css/rtl/uikit.css">
    <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>css/demo.css">
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
    <link rel="stylesheet"
          href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <!-- Page -->
    <link rel="stylesheet" href="<?php echo base_url("assets/adminv2/assets/") ?>vendor/css/pages/authentication.css">
</head>
<body>
<div class="page-loader">
    <div class="bg-primary"></div>
</div>

<!-- Content -->
<?php echo $body; ?>
<!-- / Content -->

<!-- Core scripts -->
<script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/popper/popper.js"></script>
<script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/js/bootstrap.js"></script>
<script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/js/sidenav.js"></script>
<!-- Libs -->
<script src="<?php echo base_url("assets/adminv2/assets/") ?>vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<!-- Demo -->
<script src="<?php echo base_url("assets/adminv2/assets/") ?>js/demo.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>