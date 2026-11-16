<?php
$docsPages = [
    'orm'                     => 'Введение',
    'orm-models'              => 'Модели',
    'orm-builder'             => 'Запросы',
    'orm-relations'           => 'Связи',
    'orm-collection'          => 'Коллекции',
    'orm-collection-paginate' => 'Пагинация',
    'orm-migrations'          => 'Миграции',
];
?>
<div>
    <h3 class="toc-title">Motor ORM</h3>
    <ul class="toc-links">
        <?php foreach ($docsPages as $slug => $name): ?>
            <li>
                <a href="/docs/<?= $slug ?>"<?= currentRoute() === '/docs/' . $slug ? ' class="active" aria-current="page"' : '' ?>><?= $name ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
