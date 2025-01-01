<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="<?php echo \Jidaikobo\Kontiki\Utils\Env::get('BASEPATH') ?>/admin/dashboard" class="brand-link">
    <span class="brand-text font-weight-light"><?php echo \Jidaikobo\Kontiki\Utils\Env::get('COPYRIGHT') ?></span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <?php foreach ($sidebarItems['groupedLinks'] as $group => $links) : ?>
          <li class="nav-item">
            <a href="#" class="nav-link" aria-expanded="false">
              <i class="nav-icon fas fa-folder"></i>
              <p>
                <?php echo htmlspecialchars($sidebarItems['groupNames'][$group]) ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <?php foreach ($links as $link) : ?>
                <li class="nav-item">
                  <a href="<?php echo htmlspecialchars($link['url']) ?>" class="nav-link">
                    <i class="nav-icon fas <?php echo htmlspecialchars($link['icon'] ?? 'fa-circle') ?>"></i>
                    <p><?php echo __($link['name']) ?></p>
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
