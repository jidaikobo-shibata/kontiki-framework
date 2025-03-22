<?php
/**
  * @var string $actionAttribute
  * @var string $csrfToken
  * @var string $formHtml
  * @var array $formVars
  * @var string $buttonPosition
  */
?>

<?php if (isset($formVars['description'])) : ?>
<p class="alert alert-primary"><?= e($formVars['description']) ?></p>
<?php endif; ?>

<!-- this <div> is for prevent DOMDocument from expanding a group of form elements -->
<div class="dontExpandForm">
<form action="<?= e($actionAttribute) ?>" method="post">
    <input type="hidden" name="_csrf_value" value="<?= e($csrfToken) ?>">
    <div class="row">
        <?= $formHtml ?>
    </div>

    <?php
    if ($buttonPosition == 'main') :
        include(__DIR__ . '/buttons.php');
    endif;
    ?>
</form>
</div>

<?php
include(__DIR__ . '/incFileManager.php');
