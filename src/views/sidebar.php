<?php
/**
  * @var array $sidebarItems
  */
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="<?= env('BASEURL', '#') ?>" class="brand-link" target="homepage"><span class="brand-text font-weight-light"><?= env('COPYRIGHT', '') ?></span></a>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <li class="nav-item"><a class="nav-link" href="<?= env('BASEPATH', '') ?>/dashboard"><?= __('management_portal') ?></a></li>
      <?php
        foreach ($sidebarItems as $controller => $links) :
            $dataPath = env('BASEPATH') . '/' . $controller;
            ?>
          <li class="nav-item" data-path="<?= e($dataPath) ?>">
            <a href="#" class="nav-link" aria-expanded="false">
              <i class="nav-icon fas fa-folder"></i>
              <p>
              <?= e(__("x_management", ':name Management', ['name' => __($controller)])) ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php foreach ($links as $link) : ?>
                <li class="nav-item">
                  <a href="<?= e($link['path']) ?>" class="nav-link">
                    <p><?= e($link['name']); ?></p>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </div>
</aside>
