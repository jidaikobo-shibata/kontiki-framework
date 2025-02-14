<!DOCTYPE html>
<html lang="<?= env('LANG', 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" media="all" href="https://dev.jidaikobo.dev/kontikip/assets/css/style.css">

  <!-- bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <title><?= e($pageTitle) ?></title>
</head>

<body>
<div class="wrapper">

  <!-- .content-wrapper -->
  <main class="content-wrapper">
     <?= $content ?>
  </main><!-- /.content-wrapper -->

  <!-- .main-footer -->
  <footer class="main-footer">
    <?= env('COPYRIGHT', '') ?>
  </footer><!-- /.main-footer -->
</div>
</body>
</html>
