<!DOCTYPE html>
<html lang="<?= $_ENV['LANG'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($pageTitle) ?></title>
</head>

<body class="bg-light">

<!-- Main content -->
<main class="container my-4">

<?= $content ?>

</main><!-- /Main content -->

</body>
</html>
