<nav>
    <ul class="pagination">
        <?php foreach ($pages as $page): ?>
            <?php if (isset($page['separator'])): ?>
                <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
            <?php elseif (isset($page['current'])): ?>
                <li class="page-item active"><span class="page-link"><?= $page['name'] ?></span></li>
            <?php else: ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page['page'] ?>"><?= $page['name'] ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>
