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
  <button type="submit" class="btn btn-info" name="preview" value="1" formtarget="preview"><?= __('preview') ?></button>
  <button type="submit" class="btn btn-primary"><?= $buttonText ?></button>
</form>
</div>

<?php
include(__DIR__ . '/incFileManager.php');
