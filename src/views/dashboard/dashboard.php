<?php foreach ($groupedLinks as $group => $links) : ?>
<h2 class="fs-4"><?= e($groupNames[$group]) ?></h2>
<ul>
    <?php foreach ($links as $link) : ?>
        <li>
            <a href="<?= e($link['url']) ?>"><?= e($link['name']) ?></a>
        </li>
    <?php endforeach; ?>
</ul>
<?php endforeach; ?>
