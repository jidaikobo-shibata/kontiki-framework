<form action="<?= $actionAttribute ?>" method="post">
  <input type="hidden" name="_csrf_value" value="<?= htmlspecialchars($csrfToken->getValue(), ENT_QUOTES, 'UTF-8') ?>">
  <?= $formHtml ?>
  <button type="submit">Save Changes</button>
</form>
