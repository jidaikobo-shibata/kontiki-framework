<!DOCTYPE html>
<html lang="<?php echo \Jidaikobo\Kontiki\Utils\Env::get('LANG') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($pageTitle) ?></title>
</head>

<body class="bg-light">

<!-- Main content -->
<main class="container my-4">

<?php echo $content ?>

</main><!-- /Main content -->

</body>
</html>
