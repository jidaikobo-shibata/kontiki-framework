<?php if (!empty($description)): ?>
<p class="alert alert-primary"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
<form action="<?= $actionAttribute ?>" method="post">
  <input type="hidden" name="_csrf_value" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
  <?= $formHtml ?>
  <button type="submit" class="btn btn-primary"><?= $buttonText ?></button>
  <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#uploadModal">ファイルアップロード</button>
</form>

<?php
include(__DIR__ . '/incFileManager.php');
