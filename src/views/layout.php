<?php
/**
  * @var string $lang
  * @var string $pageTitle
  * @var string $content
  */
?><!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php
    $basePath = env('BASEPATH', '');
    $faviconPath = env('ADMIN_FAVICON_PATH', '');
    if (!empty($faviconPath)) :
        echo '  <link rel="shortcut icon" href="' . $basePath . '/' . $faviconPath . '">';
    endif;
    ?>

  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

  <!-- AdminLTE JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

  <!-- Scripts -->
  <script src="<?= $basePath ?>/fileManager.js"></script>
  <script src="<?= $basePath ?>/fileManagerInstance.js"></script>
  <script src="<?= $basePath ?>/admin.js"></script>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= $basePath ?>/admin.css">

  <title><?= e($pageTitle) ?></title>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- .main-header -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-controls="main-sidebar">
          <span class="fas fa-bars" aria-hidden="true"></span>
        </a>
      </li>
    </ul>

    <ul class="navbar-nav ms-auto flex-row flex-wrap">
      <li class="nav-item">
        <a href="<?= $basePath ?>/account/settings" class="nav-link"><?= __('account_settings') ?></a>
      </li>
      <li class="nav-item">
        <a href="<?= $basePath ?>/help" class="nav-link" target="helpWindow"><?= __('help') ?></a>
      </li>
      <li class="nav-item">
        <a href="<?= $basePath ?>/logout" class="nav-link"><?= __('logout') ?></a>
      </li>
    </ul>
  </nav><!-- /.main-header -->

  <!-- sidebar -->
  <?php require 'sidebar.php'; ?>

  <!-- .content-wrapper -->
  <main class="content-wrapper">
    <section class="content-header" id="content-header">
      <div class="container-fluid">
        <div class="d-flex align-items-center gap-3">
            <h1><?= e($pageTitle) ?></h1>
        </div>
      </div>
    </section>

    <div class="content pb-5">
      <div class="container-fluid">
        <?= $content ?>
      </div>
    </div>
  </main><!-- /.content-wrapper -->

  <!-- .main-footer -->
  <footer class="main-footer">
    <?= env('COPYRIGHT', '') ?>
  </footer><!-- /.main-footer -->
</div>
</body>
</html>
