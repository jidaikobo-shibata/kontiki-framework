<?php
/**
  * @var string $viewUrl
  * @var string $actionAttribute
  * @var string $csrfToken
  * @var string $formHtml
  * @var array $formVars
  * @var string $buttonPosition
  */

if (!empty($viewUrl) && !empty($formVars['slug'])) :
    $publishedUrl = e($viewUrl) . '/' . e($formVars['slug']);
    $html = '';
    $html .= '<p id="publishedUrl" class="form-text">' . __('published_url') . ': ';
    $html .= '<a href="' . $publishedUrl . '" target="publishedPage">';
    $html .= $publishedUrl . ' ';
    $html .= '<span class="fa-solid fa-arrow-up-right-from-square" aria-label="';
    $html .= __('open_in_new_window') . '"></span></a></p>';
    echo $html;
endif; ?>

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
