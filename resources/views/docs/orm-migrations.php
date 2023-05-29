<?php $this->layout('layout-docs') ?>

<?php $this->start('title') ?>Builder<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('docs') ?>">Документация</a></li>
            <li class="breadcrumb-item active">Миграции</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<div class="mb-3">
    Миграции данных - это процесс изменения структуры БД без потери данных. Обычно миграции БД используются при разработке программного обеспечения, когда необходимо изменять таблицы или добавлять новые поля в уже существующие таблицы.
</div>

<div class="mb-3">
    Когда разработчики вносят изменения в схему базы данных, например, добавляют новые таблицы, изменяют существующие или удаляют их, необходимо чтобы эти изменения применились на всех экземплярах БД. Миграции дают возможность автоматизировать этот процесс, сделав его более надежным и быстрым.
</div>

<ul>
    <li><a href="#create-table">Создание таблицы</a></li>
    <li><a href="#delete-table">Удаление таблицы</a></li>
    <li><a href="#create-column">Создание колонок</a></li>
    <li><a href="#rename-column">Переименовывание колонок</a></li>
    <li><a href="#delete-column">Удаление колонок</a></li>
    <li><a href="#has-table">Проверка существования таблицы</a></li>
    <li><a href="#has-column">Проверка существования колонки</a></li>
    <li><a href="#up">Выполнение миграций</a></li>
    <li><a href="#down">Откат миграций</a></li>
</ul>

<div class="mb-3">
    Для вызова класса миграции необходимо в конструктор передать нужную нам модель
</div>

<pre class="prettyprint">
$migration = new Migration(new Test());
</pre>

<h3 id="create-table">Создание таблицы</h3>
Пример создания файла test.csv с пятью полями
<pre class="prettyprint">
$migration->createTable(function (Migration $table) {
    $table->create('id');
    $table->create('title');
    $table->create('text');
    $table->create('user_id');
    $table->create('created_at');
});
</pre>

<h3 id="delete-table">Удаление таблицы</h3>
<pre class="prettyprint">
$migration->deleteTable();
</pre>

<h3 id="create-column">Создание колонок</h3>
<pre class="prettyprint">
$migration->changeTable(function (Migration $table) {
    // Создаст колонку text c текстом по умолчанию "Текст" после колонки title
    $table->create('text')->default('Текст')->after('title');

    // Создаст колонку test перед колонкой id
    $table->create('test')->before('id');
});
</pre>

<h3 id="rename-column">Переименовывание колонок</h3>
<pre class="prettyprint">
$migration->changeTable(function (Migration $table) {
    // Переименует user_id в author_id
    $table->rename('user_id', 'author_id');
});
</pre>

<h3 id="delete-column">Удаление колонок</h3>
<pre class="prettyprint">
$migration->changeTable(function (Migration $table) {
    // Удалит колонку title
    $table->delete('title');
});
</pre>

<h3 id="has-table">Проверка существования таблицы</h3>
<pre class="prettyprint">
// Проверит существование таблицы
$migration->hasTable();
</pre>

<h3 id="has-column">Проверит существование колонки</h3>
<pre class="prettyprint">
// Проверит существование колонки
$migration->hasColumn('field');
</pre>

<h3 id="up">Выполнение миграций</h3>
Данная команда просканирует директорию /database/migrations, найдет все классы в этой директории и вызовет у каждого класса метод up()
<pre class="prettyprint">
php motor migrate
</pre>

<h3 id="down">Откат миграций</h3>
Команда откатывает выполненные раннее миграции
<pre class="prettyprint">
php motor migrate:rollback
</pre>

Миграции позволяют поддерживать состояние файловых таблиц в актуальном виде
