<nav>
    <ul class="pagination">
        <?php foreach($pages as $page): ?>
            <?php if(isset($page['separator'])): ?>
                <li><span><?= $page['name'] ?></span></li>
            <?php elseif(isset($page['current'])): ?>
                <li class="active"><span><?= $page['name'] ?></span></li>
            <?php else: ?>
                <li><a href="?page=<?= $page['page'] ?>"><?= $page['name'] ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>
