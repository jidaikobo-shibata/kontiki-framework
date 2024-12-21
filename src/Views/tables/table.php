<?php
use \jidaikobo\kontiki\Utils\Lang;
?>
<nav class="navbar bg-body-tertiary">
  <div class="container-fluid">
    <form method="get" action="" class="d-flex input-group" role="search">
        <label for="keywordSearch" class="input-group-text"><?= Lang::get('search_str', 'Search String')  ?></label>
        <input type="text" id="keywordSearch" name="s" value="<?= htmlspecialchars(filter_input(INPUT_GET, 's') ?? '', ENT_QUOTES) ?>" class="form-control">
        <button type="submit" class="btn btn-outline-secondary"><?= Lang::get('search', 'Search')  ?></button>
    </form>
  </div>
</nav>

<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <?= $headers ?>
        </tr>
    </thead>
    <tbody>
        <?= $rows ?>
    </tbody>
</table>
