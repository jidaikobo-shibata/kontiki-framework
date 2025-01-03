<?php if (!empty($description)) : ?>
<p class="alert alert-primary"><?= e($description) ?></p>
<?php endif; ?>

<!-- this <div> is for prevent DOMDocument from expanding a group of form elements -->
<div class="dontExpandForm">
<form action="<?= e($actionAttribute) ?>" method="post">
  <input type="hidden" name="_csrf_value" value="<?= e($csrfToken) ?>">
  <?= $formHtml ?>
  <button type="submit" class="btn btn-primary"><?= $buttonText ?></button>
  <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#uploadModal"><?= __('file_upload', 'File Upload') ?></button>
</form>
</div>

<?php
include(__DIR__ . '/incFileManager.php');
