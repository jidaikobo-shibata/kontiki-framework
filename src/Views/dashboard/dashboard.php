<?php foreach ($groupedLinks as $group => $links) : ?>
<h2 class="fs-4"><?php echo htmlspecialchars($groupNames[$group]) ?></h2>
<ul>
    <?php foreach ($links as $link) : ?>
        <li>
            <a href="<?php echo htmlspecialchars($link['url']) ?>"><?php echo htmlspecialchars($link['name']) ?></a>
        </li>
    <?php endforeach; ?>
</ul>
<?php endforeach; ?>
