<?php if (!empty($description)): ?>
<p class="alert alert-primary"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<!-- this <div> is for prevent DOMDocument from expanding a group of form elements -->
<div class="dontExpandForm">
<form action="<?= htmlspecialchars($actionAttribute, ENT_QUOTES, 'UTF-8') ?>" method="post">
  <input type="hidden" name="_csrf_value" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
  <?= $formHtml ?>
  <button type="submit" class="btn btn-primary"><?= $buttonText ?></button>
  <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#uploadModal">ファイルアップロード</button>
</form>
</div>

<?php
include(__DIR__ . '/incFileManager.php');
