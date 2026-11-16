<?php $this->layout('layout-docs') ?>

<?php $this->start('title') ?>Введение<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('docs') ?>">Документация</a></li>
            <li class="breadcrumb-item active">Введение</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<p>
    Motor ORM — библиотека, на которой Motor CMS хранит все свои данные. Она работает с обычными CSV-файлами так,
    как другие ORM работают с базой данных: модели, запросы с условиями, связи между таблицами, пагинация и миграции.
    Сервер базы данных не нужен.
</p>

<p>
    Файлы пишутся по RFC 4180: кавычка внутри значения удваивается, экранирующего символа нет. Таблицу можно открыть
    в любом редакторе таблиц, поправить руками или прочитать другим парсером.
</p>

<ul>
    <li><a href="#install">Установка</a></li>
    <li><a href="#layers">Из чего состоит</a></li>
    <li><a href="#quick-start">Быстрый старт</a></li>
    <li><a href="#example">Пример целиком</a></li>
    <li><a href="#storage">Как хранятся данные</a></li>
    <li><a href="#performance">Производительность</a></li>
    <li><a href="#limits">Пределы</a></li>
</ul>

<h3 id="install">Установка</h3>
<p>Нужны PHP 8.5 и расширение <code>mbstring</code>. В Motor CMS библиотека уже подключена, отдельно она ставится через composer:</p>
<pre class="prettyprint">
composer require visavi/motor-orm
</pre>

<h3 id="layers">Из чего состоит</h3>
<p>Код приложения работает с двумя слоями, третий скрыт внутри:</p>

<div class="table-responsive mb-3">
    <table class="table table-vcenter">
        <thead>
            <tr>
                <th>Слой</th>
                <th>Как выглядит</th>
                <th>За что отвечает</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Модель</strong></td>
                <td><code>class Story extends Model</code>, <code>$story</code></td>
                <td>таблица, типы колонок, scope, связи и значения одной строки</td>
            </tr>
            <tr>
                <td><strong>Запрос</strong></td>
                <td><code>Story::query()</code></td>
                <td>условия, сортировка, пагинация, запись</td>
            </tr>
            <tr>
                <td><strong>Таблица</strong></td>
                <td><code>Table</code>, <code>CsvFile</code>, <code>RowMapper</code></td>
                <td>чтение файла, разбор строк, отбор и сортировка</td>
            </tr>
        </tbody>
    </table>
</div>

<p>
    Прочитанная модель — это и есть строка таблицы. Условий она не хранит: они живут только в запросе, поэтому строка
    не может случайно перечитать таблицу.
</p>

<h3 id="quick-start">Быстрый старт</h3>
<p>Модель указывает, в каком файле лежит таблица:</p>
<pre class="prettyprint">
use MotorORM\Model;

class Article extends Model
{
    public string $table = __DIR__ . '/data/articles.csv';
}

$article = Article::query()->find(1);

echo $article->title;
</pre>

<p>
    Файл таблицы должен существовать: запрос к отсутствующему файлу бросает <code>UnexpectedValueException</code>,
    пустая таблица сама не создаётся. Таблицы создаются <a href="/docs/orm-migrations">миграциями</a>.
</p>

<p>
    В Motor CMS путь к таблицам уже задан в базовой модели <code>App\Models\Model</code>, поэтому моделям достаточно
    указать имя файла. Подробнее — в разделе <a href="/docs/orm-models">Модели</a>.
</p>

<h3 id="example">Пример целиком</h3>
<p>Модель со связью, типами колонок и scope, а под ней страница со списком:</p>
<pre class="prettyprint">
class Article extends Model
{
    protected string $table = 'articles.csv';

    protected array $casts = [
        'user_id' => 'int',
        'views'   => 'int',
    ];

    public function user(): Relation
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function scopePublished(Query $query): Query
    {
        return $query->where('published', 1);
    }
}

$articles = Article::query()
    ->published()
    ->whereLike('title', '%орм%')
    ->orderByDesc('created_at')
    ->paginate(10);

foreach ($articles as $article) {
    printf('%s — %s, %d просмотров', $article->title, $article->user->login, $article->views);
}

echo pagination($articles);
</pre>

<p>
    Авторы статей здесь не читаются по одному: первое обращение к <code>$article->user</code> загружает авторов
    всех десяти статей за один проход по файлу. Подробнее — в разделе <a href="/docs/orm-relations">Связи</a>.
</p>

<h3 id="storage">Как хранятся данные</h3>
<ul>
    <li>Первая строка файла — заголовки колонок.</li>
    <li>
        Первая колонка — уникальный ключ. Он может быть числовым или строковым. Числовой ключ при вставке выдаётся
        автоматически, строковый передаётся явно.
    </li>
    <li>
        Каждая запись идёт с блокировкой файла, поэтому процессы, пишущие одновременно, не затирают данные друг друга.
    </li>
    <li>
        Изменение существующих строк собирает новую таблицу рядом со старой и подменяет её одним атомарным шагом.
        Читающий процесс никогда не увидит недописанный файл, а прерванная запись оставит исходный нетронутым.
    </li>
</ul>

<h3 id="performance">Производительность</h3>
<p>
    Замер на таблице в 50 000 строк (4 МБ). Для сравнения взят код на чистом PHP: <code>fopen</code>,
    <code>fgetcsv</code> и <code>array_combine</code> в цикле, без объектов и приведения типов. Быстрее файл не прочитать,
    поэтому разница с ним — это цена ORM.
</p>

<div class="table-responsive mb-3">
    <table class="table table-vcenter">
        <thead>
            <tr>
                <th>Операция</th>
                <th class="text-end">Чистый PHP</th>
                <th class="text-end">Motor ORM</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>найти запись по ключу</td><td class="text-end text-nowrap">67.7 мс</td><td class="text-end text-nowrap"><strong>0.7 мс</strong></td></tr>
            <tr><td>посчитать строки по условию</td><td class="text-end text-nowrap">66.1 мс</td><td class="text-end text-nowrap">71.4 мс</td></tr>
            <tr><td>выбрать строки по условию</td><td class="text-end text-nowrap">67.3 мс</td><td class="text-end text-nowrap">71.9 мс</td></tr>
            <tr><td>страница из десяти строк</td><td class="text-end text-nowrap">0.1 мс</td><td class="text-end text-nowrap">0.1 мс</td></tr>
            <tr><td>десять последних с сортировкой</td><td class="text-end text-nowrap">132.5 мс, 33 МБ</td><td class="text-end text-nowrap"><strong>97.3 мс, 0.6 МБ</strong></td></tr>
            <tr><td>обойти всю таблицу</td><td class="text-end text-nowrap">66.4 мс</td><td class="text-end text-nowrap">81.4 мс</td></tr>
            <tr><td>прочитать таблицу целиком</td><td class="text-end text-nowrap">69.1 мс, 30 МБ</td><td class="text-end text-nowrap">93.7 мс, 35 МБ</td></tr>
        </tbody>
    </table>
</div>

<p>
    На полном проходе ORM медленнее на 5–35%: время уходит на разбор условий, приведение типов и создание объектов.
    Две строки выбиваются в другую сторону. <code>find()</code> вообще не читает таблицу целиком, а ищет ключ делением
    файла пополам. Сортировка с <code>limit</code> держит в памяти только строки, которые попадут в ответ, а не всю таблицу.
</p>

<h3 id="limits">Пределы</h3>
<p>
    Строка CSV в памяти PHP занимает в разы больше, чем в файле: модель на пять колонок — около 790 байт. Файл в 41 МБ,
    прочитанный целиком, займёт 377 МБ. Поэтому ограничивает не размер таблицы, а размер результата:
</p>

<div class="table-responsive mb-3">
    <table class="table table-vcenter">
        <thead>
            <tr>
                <th>500 000 строк, 41 МБ</th>
                <th class="text-end">Время</th>
                <th class="text-end">Память</th>
            </tr>
        </thead>
        <tbody>
            <tr><td><code>count()</code></td><td class="text-end">62 мс</td><td class="text-end">0 МБ</td></tr>
            <tr><td><code>paginate(10)</code></td><td class="text-end">64 мс</td><td class="text-end">0 МБ</td></tr>
            <tr><td><code>cursor()</code> по всей таблице</td><td class="text-end">797 мс</td><td class="text-end">0 МБ</td></tr>
            <tr><td><code>orderByDesc('id')->limit(10)</code></td><td class="text-end">987 мс</td><td class="text-end">0 МБ</td></tr>
            <tr><td><code>get()</code> всей таблицы</td><td class="text-end">—</td><td class="text-end">377 МБ</td></tr>
        </tbody>
    </table>
</div>

<p>
    Таблица может быть любого размера, пока не запрашивать её целиком. Для обхода есть
    <a href="/docs/orm-builder#cursor"><code>cursor()</code></a>, для вывода списков —
    <a href="/docs/orm-collection-paginate"><code>paginate()</code></a>.
</p>

<p>
    Второй предел — время. Проход стоит около 1 мкс на строку, и <code>where</code> по таблице в 500 000 строк займёт
    около половины секунды. Индексов в библиотеке нет, поэтому она рассчитана на сайты, где таблицы измеряются
    тысячами и десятками тысяч строк.
</p>
