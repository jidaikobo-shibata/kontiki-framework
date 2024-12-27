<select name="<?= $name; ?>" id="<?= $id; ?>" <?= $attributes ?><?= $ariaDescribedbyAttribute ?>>
<?php
foreach ($options as $key => $value) :
    ?>
<option value="<?= $key ?>"><?= $value ?></option>
    <?php
endforeach;
?>
</select>
<?= $description ?>
