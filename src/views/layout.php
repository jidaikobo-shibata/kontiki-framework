<!DOCTYPE html>
<html lang="<?= $_ENV['LANG'] ?? 'en' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- AdminLTE JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

  <!-- Scripts -->
<?php if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) : ?>
  <script src="<?= $_ENV['BASEPATH'] ?? '' ?>/admin/fileManager.js"></script>
  <script src="<?= $_ENV['BASEPATH'] ?? '' ?>/admin/fileManagerInstance.js"></script>
<?php endif; ?>

  <title><?= htmlspecialchars($pageTitle) ?></title>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- .main-header -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav ml-auto">
    <li class="nav-item d-none d-sm-inline-block">
    <a href="<?= $_ENV['BASEPATH'] ?? '' ?>/logout" class="nav-link"><?= __('logout', 'Logout') ?></a>
    </li>
  </ul>
  </nav><!-- /.main-header -->

  <!-- sidebar -->
  <?php require 'sidebar.php'; ?>

  <!-- .content-wrapper -->
  <main class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
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
    <?= $_ENV['COPYRIGHT'] ?? '' ?>
  </footer><!-- /.main-footer -->
</div>
</body>
</html>
