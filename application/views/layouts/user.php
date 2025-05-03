<?php
$app_name = @$web_app['app_name'];
$app_name_3 = get3Initials($app_name);
?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="author" content="anwarsptr.com">
  <meta name="description" content="<?= $web_app['description'] ?>" />
  <meta name="keywords" content="<?= $web_app['description'] ?>">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="HandheldFriendly" content="True">
  <meta name="csrf-token" content="<?= setCSRF() ?>">

  <title><?= $title ?> - <?= @$web_app['title']; ?></title>
  <base href="<?= $base_url ?>">
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="<?= checkImg(@$web_app['favicon'],'favicon') ?>" />
  <?php view("includes/user/style") ?>
  <script>
    var BASEURL = document.querySelector('base').href;
  </script>
</head>

<body class="fixed sidebar-mini sidebar-mini-expand-feature layout-boxed skin-blue-light">
  <!-- Layout wrapper -->
  <div class="wrapper">
    <header class="main-header">
      <!-- Logo -->
      <a href="<?= $base_url ?>" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><?= $app_name_3 ?></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><?= $app_name; ?></span>
      </a>
      <?php view("includes/user/navbar") ?>
    </header>
    <?php view("includes/user/menu") ?>
    <div class="content-wrapper">
      	<?php view($content); ?>
    </div>
    <footer class="main-footer">
      <div class="pull-right hidden-xs">
        <b>Version</b> v1.0
      </div>
      <strong>
      Copyright ©
      <script>
        document.write(new Date().getFullYear());
      </script> <b><?= $app_name ?>. <span style="font-weight: 500">All Right Reserved</span>.</b>
    </footer>

</div>
<!-- / Layout wrapper -->

<?php view("includes/user/script") ?>

</body>
</html>
