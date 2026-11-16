<?php $this->layout('layout-docs') ?>

<?php $this->start('title') ?>Модели<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('docs') ?>">Документация</a></li>
            <li class="breadcrumb-item active">Модели</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<p>
    Модель описывает таблицу: в каком файле она лежит, какого типа колонки, какие у неё связи и готовые условия.
    Прочитанная из файла модель — это одна строка таблицы со своими значениями.
</p>

<ul>
    <li><a href="#declare">Объявление модели</a></li>
    <li><a href="#row">Модель как строка</a></li>
    <li><a href="#methods">Свои методы строки</a></li>
    <li><a href="#casts">Приведение типов (casts)</a></li>
    <li><a href="#scopes">Готовые условия (scope)</a></li>
</ul>

<h3 id="declare">Объявление модели</h3>
<p>Достаточно унаследовать <code>MotorORM\Model</code> и указать путь к файлу:</p>
<pre class="prettyprint">
use MotorORM\Model;

class Article extends Model
{
    public string $table = __DIR__ . '/data/articles.csv';
}
</pre>

<p>
    Когда таблиц много, общий каталог удобнее задать один раз в свойстве <code>$tableDir</code> базового класса.
    Так устроена Motor CMS: все модели наследуют <code>App\Models\Model</code>, а таблицы лежат в
    <code>storage/database</code>.
</p>
<pre class="prettyprint">
namespace App\Models;

use MotorORM\Model as BaseModel;

abstract class Model extends BaseModel
{
    protected ?string $tableDir = __DIR__ . '/../../storage/database';
}

class Story extends Model
{
    protected string $table = 'stories.csv';
}
</pre>

<h3 id="row">Модель как строка</h3>
<p>
    <code>find()</code>, <code>first()</code> и <code>create()</code> возвращают модель, <code>get()</code> — коллекцию
    моделей. Колонки читаются и меняются как свойства, а сохранение — отдельный шаг:
</p>
<pre class="prettyprint">
$story = Story::query()->find(1);

$story->title;                      // значение колонки
$story->title = 'Новый заголовок';  // изменено только в памяти
$story->save();                     // записано в файл

$story->update(['text' => 'Новый текст']);  // изменить и сразу сохранить
$story->delete();                           // удалить строку из файла
$story->fresh();                            // перечитать из файла, отбросив несохранённое
$story->toArray();                          // значения обычным массивом
</pre>

<p>Новая строка создаётся так же: модель без ключа при сохранении вставляется, а ключ ей выдаёт таблица.</p>
<pre class="prettyprint">
$story = new Story();
$story->title = 'Новая статья';
$story->save();

echo $story->id;
</pre>

<p>Чтобы вставить строку одним вызовом, есть <code>create()</code> у запроса — см. раздел <a href="/docs/orm-builder#write">Запись</a>.</p>

<h3 id="methods">Свои методы строки</h3>
<p>Всё, что строка может ответить о себе, удобно держать методом модели, рядом с колонками:</p>
<pre class="prettyprint">
class Story extends Model
{
    protected string $table = 'stories.csv';

    public function excerpt(int $words = 30): string
    {
        return implode(' ', array_slice(explode(' ', $this->text), 0, $words));
    }
}

Story::query()->find(1)->excerpt();
</pre>

<p>Связанная запись тоже читается в свою модель, поэтому <code>$story->user</code> умеет всё, что умеет <code>User</code>.</p>

<h3 id="casts">Приведение типов (casts)</h3>
<p>
    CSV не хранит типов, поэтому все значения из файла читаются строками, а пустое значение — как <code>null</code>.
    Исключение одно: числовой первичный ключ читается как <code>int</code>.
</p>

<p>
    Остальное ORM не угадывает, в том числе по имени колонки: <code>created_at</code> со значением
    <code>1787922565</code> останется строкой. Типы колонок задаются в свойстве <code>$casts</code>:
</p>
<pre class="prettyprint">
class Story extends Model
{
    protected array $casts = [
        'active'     => 'bool',
        'rating'     => 'int',
        'user_id'    => 'int',
        'created_at' => 'int',
        'meta'       => 'array',
    ];
}
</pre>

<div class="table-responsive mb-3">
    <table class="table table-vcenter">
        <thead>
            <tr>
                <th>Тип</th>
                <th>Что получится</th>
            </tr>
        </thead>
        <tbody>
            <tr><td><code>int</code>, <code>integer</code></td><td><code>int</code></td></tr>
            <tr><td><code>real</code>, <code>float</code>, <code>double</code></td><td><code>float</code></td></tr>
            <tr><td><code>string</code></td><td><code>string</code></td></tr>
            <tr><td><code>bool</code>, <code>boolean</code></td><td><code>bool</code></td></tr>
            <tr><td><code>array</code></td><td><code>json_decode($value, true)</code></td></tr>
            <tr><td><code>object</code></td><td><code>json_decode($value, false)</code></td></tr>
        </tbody>
    </table>
</div>

<ul>
    <li>
        Касты меняют только то, что вы читаете. Условия и сортировка работают с сырыми значениями файла, поэтому
        <code>where('id', 1)</code> ведёт себя одинаково, объявлен <code>id</code> в <code>$casts</code> или нет.
    </li>
    <li>Числовой ключ можно оставить строкой, объявив его как <code>'id' => 'string'</code>.</li>
    <li>
        Массивы и объекты записываются в колонку как JSON. Если в колонке лежит невалидный JSON, чтение бросает
        <code>UnexpectedValueException</code>, а не тихо возвращает <code>null</code>.
    </li>
</ul>

<h3 id="scopes">Готовые условия (scope)</h3>
<p>
    Условие, которое повторяется в разных запросах, выносится в метод модели с префиксом <code>scope</code>.
    Метод получает запрос и возвращает его с добавленными условиями, а вызывается без префикса:
</p>
<pre class="prettyprint">
class Story extends Model
{
    public function scopeActive(Query $query): Query
    {
        return $query->where('active', true);
    }
}

Story::query()->active()->paginate(10);
</pre>

<p>Параметры после <code>$query</code> передаются при вызове:</p>
<pre class="prettyprint">
class Story extends Model
{
    public function scopeOfUser(Query $query, int $userId): Query
    {
        return $query->where('user_id', $userId);
    }
}

Story::query()->active()->ofUser(5)->get();
</pre>
