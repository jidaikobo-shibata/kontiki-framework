<?php
$ariaDescribedby = '';
$ariaDescribedbyAttribute = '';
if (!empty($description)):
  $ariaDescribedby = 'ariaDesc_' . $id;
  $ariaDescribedbyAttribute = ' aria-describedby="' . $ariaDescribedby . '"';
  $description = '<div class="form-text" id="' . $ariaDescribedby . '">' . $description . '</div>';
endif;
?>
<input type="<?= $type; ?>" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= $value; ?>"<?= $attributes ?><?= $ariaDescribedbyAttribute ?>>
<?= $description ?>
