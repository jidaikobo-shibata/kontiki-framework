<?php
/**
  * @var string $actionAttribute
  * @var string $csrfToken
  * @var string $formHtml
  * @var string $formVars
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
  <?php if ($is_previewable === true) : ?>
  <button type="submit" class="btn btn-info" name="preview" value="1" formtarget="preview">
    <?= __('preview') ?>
    <span class="fa-solid fa-arrow-up-right-from-square" aria-label="<?= __('open_in_new_window') ?>"></span>
  </button>
  <?php endif; ?>
  <button id="<?= $formVars['buttonID'] ?? '' ?>" type="submit" class="btn <?= $formVars['buttonClass'] ?? 'btn-primary' ?>"><?= $formVars['buttonText'] ?? '' ?></button>
</form>
</div>

<?php
include(__DIR__ . '/incFileManager.php');
