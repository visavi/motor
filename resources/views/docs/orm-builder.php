<?php $this->layout('layout-docs') ?>

<?php $this->start('title') ?>Builder<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('docs') ?>">Документация</a></li>
            <li class="breadcrumb-item active">Builder</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<div class="mb-3">
    Данный скрипт предоставляет ООП подход для работы текстовыми данными сохраненными в файловой системе
</div>
<div class="mb-3">
    Структура данных CSV совместима, но с некоторыми изменения для более быстрой работы
</div>

<h3>Возможности</h3>
<ul>
    <li><a href="#model">Модели</a></li>
    <li><a href="#find">Поиск по первичному ключу</a></li>
    <li><a href="#where">Поиск (where)</a></li>
    <li><a href="#where-in">Поиск (whereIn)</a></li>
    <li><a href="#where-not-in">Поиск (whereNotIn)</a></li>
    <li><a href="#or-where">Поиск (orWhere)</a></li>
    <li><a href="#limit-offset">Limit - Offset</a></li>
    <li><a href="#headers">Возврат структуры файла</a></li>
    <li><a href="#count">Возврат количества записей в файле</a></li>
    <li><a href="#exists">Возврат информации о существовании записи</a></li>
    <li><a href="#order">Сортировка строк</a></li>
    <li><a href="#create">Создание записей</a></li>
    <li><a href="#update">Обновление записей</a></li>
    <li><a href="#delete">Удаление записей</a></li>
    <li><a href="#truncate">Очистка файла</a></li>
    <li><a href="#like">Частичный поиск (Like)</a></li>
    <li><a href="#lax">Нестрогий поиск (Lax)</a></li>
    <li><a href="#casts">Приведение типов (Casts)</a></li>
    <li><a href="#scope">Условия запросов (Scope)</a></li>
    <li><a href="#dynamic-scope">Динамические условия</a></li>
    <li><a href="#when">Условные выражения (Conditional clauses)</a></li>
    <li><a href="#relations">Связи</a></li>
    <li><a href="#has-one">Связь один к одному</a></li>
    <li><a href="#has-many">Связь один ко многим</a></li>
    <li><a href="#has-many-through">Связь многие ко многим</a></li>
    <li><a href="#eager-load">Жадная загрузка</a></li>
</ul>

<div class="mb-3">
    Работы с изменениями в файле, в том числе и вставка выполняется с блокировкой файла для защиты от случайного удаления данных в случае если несколько пользователей одновременно пишут в файл
</div>

<div class="mb-3">
    Первых столбец в файле считается уникальным<br>
    Может быть строковым и числовым<br>
    Если столбец строковой, то все вставки должны быть с уже заданным уникальным ключом<br>
    Если столбец числовой, то уникальный ключ будет генерироваться автоматически
</div>


<h3>Запросы</h3>
<div class="mb-3">
    Все запросы проводятся через модели в котором должен быть указан путь к файлу с данными В самих моделях могут быть реализованы дополнительные методы
</div>

<h3 id="model">Модель</h3>

Все запросы выполняются через модель, для начала работы необходимо создать класс модели в котором нужно указать путь к таблице
<pre class="prettyprint">
&lt;?php

use MotorORM\Builder;

class TestModel extends Builder
{
    public string $table = __DIR__ . '/test.csv';
}
</pre>

Также в базовом классе можно указать директорию с таблицами, а в самом классе модели достаточно указать имя файла

<pre class="prettyprint">
&lt;?php

namespace App\Models;

use MotorORM\Builder;

class Model extends Builder
{
    protected ?string $tableDir = __DIR__ . '/../../storage/database';
}

class User extends Model
{
    protected string $table = 'users.csv';
}
</pre>

<h3 id="find">Поиск по первичному ключу</h3>
<pre class="prettyprint">
TestModel::query()->find(1);

# Если первичный ключ строковой
TestModel::query()->find('value');
</pre>

<h3 id="where">Поиск по заданным значениям</h3>

<pre class="prettyprint">
# Find by name limit 1
TestModel::query()->where('name', 'Миша')->limit(1)->get();

# Find by name and first 1
TestModel::query()->where('name', 'Миша')->first();

# Find by name and title
TestModel::query()->where('name', 'Миша')->where('title', 'Заголовок10')->get();

# Get from condition
TestModel::query()->where('time', '>=', 1231231235)->get();

# Get first line
TestModel::query()->first();
</pre>

<h3 id="where-in">Поиск (whereIn)</h3>
<pre class="prettyprint">
TestModel::query()->whereIn('id', [1, 3, 4, 7])->get();
</pre>

<h3 id="where-not-in">Поиск (whereNotIn)</h3>
<pre class="prettyprint">
TestModel::query()->whereNotIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->get();
</pre>

<h3 id="or-where">Поиск (orWhere)</h3>
<pre class="prettyprint">
# Get records by multiple conditions
TestModel::query()
    ->where(function(Builder $builder) {
        $builder->where('name', 'Миша');
        $builder->orWhere(function(Builder $builder) {
            $builder->where('name', 'Петя');
            $builder->where('title', '<>', '');
        });
    })
    ->get();
</pre>

<h3 id="limit-offset">Limit - offset</h3>
<pre class="prettyprint">
# Get first 3 lines
TestModel::query()->limit(3)->get();

# Get lines 1 - 10
$lines = TestModel::query()->offset(0)->limit(10)->get();

# Get last 10 records
$lines = TestModel::query()->orderByDesc('created_at')->offset(0)->limit(10)->get();
</pre>


<h3 id="count">Получение количества записей</h3>
<pre class="prettyprint">
TestModel::query()->where('time', '>', 1231231234)->count();
</pre>

<h3 id="exists">Проверка существования записи</h3>
<pre class="prettyprint">
TestModel::query()->where('field', 'value')->exists();
</pre>

<h3 id="order">Сортировка записей</h3>
<pre class="prettyprint">
# Сортировка
TestModel::query()->orderByDesc('created_at')->limit(3)->get();

# Двойная сортировка (time desc, id asc)
Test::query()
    ->where('name', 'Миша')
    ->orderByDesc('time')
    ->orderBy('id')
    ->limit(3)
    ->get();
</pre>

<h3 id="headers">Получение колонок</h3>
<pre class="prettyprint">
TestModel::query()->headers();
</pre>

<h3 id="create">Создание записей</h3>
<pre class="prettyprint">
TestModel::query()->create(['name' => 'Миша']);
</pre>

<h3 id="update">Обновление записей</h3>
<pre class="prettyprint">
# Обновление полей
TestModel::query()->where('name', 'Миша')->update(['text' => 'Новый текст']);

# Сохранение записи
$test = TestModel::query()->where('name', 'Миша')->first();
$test->text = 'Новый текст';
$test->save();

# Обновление полей
$testModel = TestModel::query()->find(17);
$affectedLines = $testModel->update(['text' => 'Новый текст']);
</pre>

<h3 id="delete">Удаление записей</h3>
<pre class="prettyprint">
# Удаление записи
TestModel::query()->where('name', 'Миша')->delete();

# Удаление записей в цикле
$records = TestModel::query()->get();
foreach($records as $record) {
    $record->delete();
}
</pre>

<h3 id="truncate">Очистка таблицы</h3>
<pre class="prettyprint">
# Truncate file
TestModel::query()->truncate();
</pre>

<h3 id="like">Частичный поиск (Like)</h3>
Поиск по частичному совпадению

<pre class="prettyprint">
// Строки начинающиеся на hi
$test = TestModel::query()->where('tag', 'like', 'hi%')->get();

// Строки заканчивающиеся на hi
$test = TestModel::query()->where('tag', 'like', '%hi')->get();

// Строки содержащие hi
$test = TestModel::query()->where('tag', 'like', '%hi%')->get();

// Этот запрос эквивалентен запросу выше
$test = TestModel::query()->where('tag', 'like', 'hi')->get();
</pre>

<h3 id="lax">Нестрогий поиск (Lax)</h3>

Поиск по нестрогому совпадению

При поиске orm использует строгое сравнение, чтобы задействовать нестрогий режим, можно использовать lax

<pre class="prettyprint">
// Будут найдено первое совпадение NAME, name, namE, Name итд
$user = User::query()->where('login', 'lax', 'name')->first();
</pre>

<h3 id="casts">Приведение типов (Casts)</h3>

По умолчанию все поля полученные из файла строковые

За некоторыми исключениями

Поле primary key - int
Поля заканчивающиеся на _id и _at - int
Пустые поля - null
Для переопределения используйте свойство casts

<pre class="prettyprint">
class Story extends Model
{
    protected array $casts = [
        'rating' => 'int',
        'reads'  => 'int',
        'locked' => 'bool',
    ];
}
</pre>

Поддерживаются следующие типы

<pre class="prettyprint">
'int', 'integer' => int
'real', 'float', 'double' => float
'string' => string
'bool', 'boolean' => bool
'object' => json_decode($value, false),
'array' => json_decode($value, true),
</pre>

<h3 id="scope">Условия запросов (Scope)</h3>

Каждый scope — это обычный метод, который начинается с префикса scope. Именно по префиксу ORM понимает, что это scope. Внутрь scope передаётся запрос, на который можно навешивать дополнительные условия.

<pre class="prettyprint">
class Story extends Model
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
</pre>
Использование:

<pre class="prettyprint">
Story::query()
    ->active()
    ->paginate($perPage);
</pre>

<h3 id="dynamic-scope">Динамические условия</h3>

Некоторые scope зависят от параметров, передающихся в процессе составления запроса. Для этого достаточно описать эти параметры внутри scope после параметра $query:

<pre class="prettyprint">
class Story extends Model
{
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
</pre>
Использование:

<pre class="prettyprint">
Story::query()
    ->ofType('new')
    ->paginate($perPage);
</pre>

<h3 id="when">Условные выражения (Conditional clauses)</h3>

Иногда вам может понадобиться, чтобы определенный запроса выполнялся на основе другого условия. Например, вы можете захотеть применить where оператор только в том случае, если заданное входное значение присутствует во входящем HTTP-запросе. Вы можете сделать это, используя when метод:

<pre class="prettyprint">
$active = true;

$stories = Story::query()
    ->when($active, function (Story $query, $active) {
        $query->where('active', $active);
    })
    ->get();
</pre>

Метод when выполняет данное замыкание только тогда, когда первый аргумент равен true. Если первый аргумент равен false, замыкание не будет выполнено.

Вы можете передать другое замыкание в качестве третьего аргумента when метода. Это замыкание будет выполняться только в том случае, если первый аргумент оценивается как false. Чтобы проиллюстрировать, как можно использовать эту функцию, мы будем использовать ее для настройки порядка запросов по умолчанию:

<pre class="prettyprint">
$sortByVotes = 'sort_by_votes';

$users = Story::query()
    ->when($sortByVotes, function ($query, $sortByVotes) {
        $query->orderBy('votes');
    }, function ($query) {
        $query->orderBy('name');
    })
    ->get();
</pre>

<h3 id="relations">Связи (Relations)</h3>

В данный момент поддерживается 3 вида связей

hasOne - один к одному
hasMany - один ко многим
hasManyThrough - многие ко многим

<h3 id="has-one">Один к одному (hasOne)</h3>
3 параметра, имя класса, внешний и внутренний ключ

Внешний и внутренний ключ определяются автоматически, за исключением когда имена полей не совпадают с именем класса или если связь обратная belongsTo (Возможно в будущем это будет реализовано)

<pre class="prettyprint">
// Прямая связь
class User extends Model
{
    public function story(): Builder
    {
        return $this->hasOne(Story::class);
    }
}
</pre>

<pre class="prettyprint">
// Обратная связь
class Story extends Model
{
    public function user(): Builder
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
</pre>

<h3 id="has-many">Один ко многим (hasMany)</h3>

3 параметра, имя класса, внешний и внутренний ключ

Внешний и внутренний ключ определяются автоматически, за исключением когда имена полей не совпадают с именем класса

<pre class="prettyprint">
class Story extends Model
{
    public function comments(): Builder
    {
        return $this->hasMany(Comment::class);
    }
}
</pre>

<h3 id="has-many-through">Многие ко многим (hasManyThrough)</h3>

5 параметров, имя конечного класса, имя промежуточного класса, внешние и внутренние ключи

Внешние и внутренние ключи определяются автоматически, за исключением когда имена полей не совпадают с именами классов

<pre class="prettyprint">
class Story extends Model
{
    public function tags(): Builder
    {
        return $this->hasManyThrough(Tag::class, TagStory::class);
    }
}
</pre>

<h3 id="eager-load">Жадная загрузка (Eager load)</h3>

По умолчанию все связи с ленивой загрузкой (lazy load)

Связь не будет загружена до тех пор, пока явно не будет вызвана

<div class="mb-3">
Для того чтобы жадно загрузить данные необходимо вызвать метод with и передать имена связей, которые требуется жадно загрузить
</div>

<pre class="prettyprint">
class StoryRepository implements RepositoryInterface
{
    public function getStories(int $perPage): CollectionPaginate
    {
        return Story::query()
        ->orderByDesc('locked')
        ->orderByDesc('created_at')
        ->with(['user', 'comments'])
        ->paginate($perPage);
        }
    }
}
</pre>
Жадная загрузка извлекает данные используя всего несколько запросов. Это позволяет избежать проблемы N + 1.

Представьте, что у вас есть этот код, который находит 10 сообщений, а затем отображает имя автора каждого сообщения.

<pre class="prettyprint">
foreach ($storyRepository->getStories(10) as $story) {
    echo $story->user->login;
}
</pre>

<div class="mb-3">
Без ленивой загрузки при каждой итерации цикла было бы обращение в файловую систему для получения данных, то есть 1 запрос на получение списка постов и 10 на получение пользователей
</div>

<div class="mb-3">
Жадная загрузка избавляет от этой проблемы, 1 запрос на получение списка постов и 1 на получение пользователей этих постов
</div>
