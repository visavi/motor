<?php $this->layout('layout-docs') ?>

<?php $this->start('title') ?>Миграции<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('docs') ?>">Документация</a></li>
            <li class="breadcrumb-item active">Миграции</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<p>
    Миграции меняют структуру таблиц: создают и удаляют файлы, добавляют, переименовывают и удаляют колонки, не теряя
    данных. Каждое изменение хранится отдельным файлом в репозитории, поэтому на любой копии сайта структуру можно
    привести к актуальной одной командой.
</p>

<ul>
    <li><a href="#api">Как устроена миграция</a></li>
    <li><a href="#create-table">Создание таблицы</a></li>
    <li><a href="#delete-table">Удаление таблицы</a></li>
    <li><a href="#create-column">Добавление колонок</a></li>
    <li><a href="#rename-column">Переименование колонок</a></li>
    <li><a href="#delete-column">Удаление колонок</a></li>
    <li><a href="#batch">Несколько изменений сразу</a></li>
    <li><a href="#exists">Проверка существования</a></li>
    <li><a href="#files">Файлы миграций в Motor CMS</a></li>
    <li><a href="#commands">Выполнение и откат</a></li>
</ul>

<h3 id="api">Как устроена миграция</h3>
<p>В конструктор передаётся модель таблицы:</p>
<pre class="prettyprint">
use MotorORM\Migration;

$migration = new Migration(new Story());
</pre>

<p>
    Изменения перечисляются по цепочке, а последний вызов — <code>createTable()</code> или <code>changeTable()</code> —
    применяет их к файлу.
</p>

<h3 id="create-table">Создание таблицы</h3>
<pre class="prettyprint">
$migration
    ->create('id')
    ->create('title')
    ->create('text')
    ->create('user_id')
    ->create('created_at')
    ->createTable();
</pre>

<p>Первая колонка становится уникальным ключом таблицы.</p>

<h3 id="delete-table">Удаление таблицы</h3>
<pre class="prettyprint">
$migration->deleteTable();
</pre>

<h3 id="create-column">Добавление колонок</h3>
<pre class="prettyprint">
$migration
    # Колонка text со значением «Текст» у существующих строк, после колонки title
    ->create('text')->default('Текст')->after('title')

    # Колонка slug перед колонкой text
    ->create('slug')->before('text')

    ->changeTable();
</pre>

<p>
    Без <code>after()</code> и <code>before()</code> колонка добавляется в конец, без <code>default()</code> у существующих
    строк она будет пустой. Не ставьте колонку перед первой: первая колонка — ключ таблицы, и новая займёт его место.
</p>

<h3 id="rename-column">Переименование колонок</h3>
<pre class="prettyprint">
$migration->rename('user_id', 'author_id')->changeTable();
</pre>

<h3 id="delete-column">Удаление колонок</h3>
<pre class="prettyprint">
$migration->delete('title')->changeTable();
</pre>

<h3 id="batch">Несколько изменений сразу</h3>
<p>
    Всё, что перечислено до одного <code>changeTable()</code>, применяется за один проход по файлу. Изменения
    выполняются по порядку, поэтому колонка, добавленная раньше, уже видна следующему изменению:
</p>
<pre class="prettyprint">
$migration
    ->create('slug')->after('id')
    ->create('views')->default(0)->after('slug')
    ->rename('user_id', 'author_id')
    ->delete('old_field')
    ->changeTable();
</pre>

<h3 id="exists">Проверка существования</h3>
<pre class="prettyprint">
$migration->hasTable();             // есть ли файл таблицы
$migration->hasColumn('slug');      // есть ли колонка
</pre>

<p>Оба метода только проверяют и ничего не создают.</p>

<h3 id="files">Файлы миграций в Motor CMS</h3>
<p>
    Миграции лежат в <code>database/migrations</code>. Имя файла начинается с даты и времени, по ним определяется
    порядок выполнения: <code>2026_08_29_100000_add_online_at_to_users.php</code>. Файл возвращает объект с методами
    <code>up()</code> — применить изменение — и <code>down()</code> — отменить его:
</p>
<pre class="prettyprint">
&lt;?php

use App\Models\User;
use MotorORM\Migration;

return new class
{
    public function up(): void
    {
        $migration = new Migration(new User());
        $migration
            ->create('online_at')->default(0)->after('created_at')
            ->changeTable();
    }

    public function down(): void
    {
        $migration = new Migration(new User());
        $migration
            ->delete('online_at')
            ->changeTable();
    }
};
</pre>

<p><code>down()</code> должен в точности отменять <code>up()</code>, иначе откат оставит таблицу в промежуточном состоянии.</p>

<h3 id="commands">Выполнение и откат</h3>
<p>Выполнить все новые миграции:</p>
<pre class="prettyprint">
php motor migrate
</pre>

<p>
    Команда находит в <code>database/migrations</code> файлы, которых ещё нет в таблице <code>migrations</code>, и
    вызывает у каждого <code>up()</code>. Миграции, выполненные одним запуском, записываются одной партией.
</p>

<p>Откатить последнюю партию:</p>
<pre class="prettyprint">
php motor migrate:rollback
</pre>

<p>
    Команда вызывает <code>down()</code> у миграций последнего запуска в обратном порядке. Если миграция упала
    с ошибкой, выполнение останавливается, а следующие миграции не запускаются.
</p>
