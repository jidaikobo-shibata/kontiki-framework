<?php
/**
  * @var string $actionAttribute
  * @var string $csrfToken
  * @var string $formHtml
  * @var string $buttonText
  */
?>

<?php if (!empty($description)) : ?>
<p class="alert alert-primary"><?= e($description) ?></p>
<?php endif; ?>

<!-- this <div> is for prevent DOMDocument from expanding a group of form elements -->
<div class="dontExpandForm">
<form action="<?= e($actionAttribute) ?>" method="post">
  <input type="hidden" name="_csrf_value" value="<?= e($csrfToken) ?>">
  <div class="row">
     <?= $formHtml ?>
  </div>
  <?php if (strpos($actionAttribute, '/create') !== false || strpos($actionAttribute, '/edit') !== false) : ?>
  <button type="submit" class="btn btn-info" name="preview" value="1" formtarget="preview"><?= __('preview') ?></button>
  <?php endif; ?>
  <button type="submit" class="btn btn-primary"><?= $buttonText ?></button>
</form>
</div>

<?php
include(__DIR__ . '/incFileManager.php');
