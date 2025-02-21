<?php
/**
  * @var array $displayModes
  * @var string $headers
  * @var string $rows
  */
?>
<nav class="navbar navbar-expand-lg bg-secondary-subtle" aria-label="<?= __('display_filter') ?>">
  <div class="container-fluid">
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav">
        <?php
        $html = '';
        foreach ($displayModes as $displayMode):
            $html.= '<li class="nav-item">';
            $current = strpos($_SERVER['REQUEST_URI'], $displayMode['path']) !== false ?
                ' active" aria-current="page' :
                '';

            $html.= '<a href="' . env('BASEPATH') . $displayMode['path'] . '" class="nav-link' . $current . '">';
            $html.= $displayMode['name'];
            $html.= '</a>';
            $html.= '</li>';
        endforeach;
        echo $html;
        ?>
      </ul>
    </div>
  </div>
</nav>

<nav class="navbar bg-secondary-subtle mb-3">
  <div class="container-fluid">
    <form method="get" action="" class="d-flex input-group" role="search">
        <label for="keywordSearch" class="input-group-text"><?= __('search_str', 'Search String')  ?></label>
        <input type="text" id="keywordSearch" name="s" value="<?= e(filter_input(INPUT_GET, 's') ?? '') ?>" class="form-control">
        <button type="submit" class="btn btn-outline-secondary"><?= __('search', 'Search')  ?></button>
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
