<?php
/** @var string[] $dirs */
$title = 'Нет прав на запись';
ob_start();
?>
<h1>Нет прав на запись</h1>
<p>Сайт хранит данные в файлах и не может писать в эти папки:</p>
<ul>
    <?php foreach ($dirs as $dir): ?>
        <li><code><?= htmlspecialchars($dir) ?></code></li>
    <?php endforeach; ?>
</ul>
<p>
    Разрешите запись в них в панели хостинга или FTP-клиенте — обычно это права <code>755</code> или <code>775</code>
    на папку — и обновите страницу.
</p>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
