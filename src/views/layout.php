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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
  <ul class="navbar-nav ml-auto">
    <li class="nav-item d-none d-sm-inline-block">
    <a href="<?= $basePath ?>/account/settings" class="nav-link"><?= __('account_settings') ?></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
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
