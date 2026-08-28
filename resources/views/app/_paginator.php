<?php

use MotorORM\Page;

/** @var Page[] $pages */
?>
<nav>
    <ul class="pagination">
        <?php foreach ($pages as $page): ?>
            <?php if ($page->separator): ?>
                <li class="page-item disabled" aria-disabled="true"><span class="page-link"><?= escape((string) $page->name) ?></span></li>
            <?php elseif ($page->current): ?>
                <li class="page-item active"><span class="page-link"><?= escape((string) $page->name) ?></span></li>
            <?php else: ?>
                <li class="page-item"><a class="page-link" href="<?= escape($page->url) ?>"><?= escape((string) $page->name) ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>
