<?php
$title = 'Установка завершена';
ob_start();
?>
<h1>Установка завершена</h1>
<p>Таблицы созданы, сайт готов к работе. Осталось назначить владельца:</p>
<ol>
    <li><a href="/register">Зарегистрируйтесь</a> на сайте.</li>
    <li>
        Откройте по FTP файл <code>storage/database/users.csv</code> и в своей строке замените значение колонки
        <code>role</code> (<code>user</code> или <code>pended</code>) на <code>boss</code>.
    </li>
    <li>Войдите на сайт — в меню пользователя появится админ-панель.</li>
</ol>
<p><a class="button" href="/register">Зарегистрироваться</a></p>
<p class="muted">Эта страница показывается один раз. Дальше по этому адресу открывается сайт.</p>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
