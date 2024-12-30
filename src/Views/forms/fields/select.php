<select name="<?= $name; ?>" id="<?= $id; ?>" <?= $attributes ?><?= $ariaDescribedbyAttribute ?>>
<?php
foreach ($options as $optionKey => $optionValue) :
$selected = $optionKey == $value ? ' selected="selected"' : '' ;
?>
<option <?= $selected ?>value="<?= $optionKey ?>"><?= $optionValue ?></option>
<?php
endforeach;
?>
</select>
<?= $description ?>
