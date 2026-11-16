<?php $this->layout('layout-docs') ?>

<?php $this->start('title') ?>Связи<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('docs') ?>">Документация</a></li>
            <li class="breadcrumb-item active">Связи</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<p>
    Связь — это метод модели, который возвращает <code>Relation</code>. Вызывать его не нужно: связь читается как
    свойство с тем же именем, а ORM сама решает, когда и сколько данных прочитать.
</p>

<ul>
    <li><a href="#keys">Ключи связи</a></li>
    <li><a href="#has-one">Один к одному (hasOne)</a></li>
    <li><a href="#has-many">Один ко многим (hasMany)</a></li>
    <li><a href="#has-many-through">Многие ко многим (hasManyThrough)</a></li>
    <li><a href="#constrain">Постоянные условия связи (constrain)</a></li>
    <li><a href="#loading">Как загружаются связи</a></li>
    <li><a href="#with">Загрузка вместе с запросом (with)</a></li>
</ul>

<h3 id="keys">Ключи связи</h3>
<p>Методы связей принимают класс связанной модели и два ключа:</p>
<ul>
    <li><strong>второй аргумент</strong> — колонка в связанной таблице;</li>
    <li><strong>третий аргумент</strong> — колонка в текущей таблице, по умолчанию первичный ключ.</li>
</ul>
<p>
    Связь находит строки, у которых значение второй колонки совпадает со значением третьей. Если ключи не указаны,
    они выводятся из имён классов: для <code>hasMany(Comment::class)</code> в модели <code>Story</code> это
    <code>comments.story_id = stories.id</code>.
</p>

<h3 id="has-one">Один к одному (hasOne)</h3>
<p>
    Возвращает одну модель. В прямой связи ссылка лежит в связанной таблице, и ключи выводятся сами — так же, как
    у <code>hasMany</code>. Например, профиль пользователя с колонкой <code>user_id</code>:
</p>
<pre class="prettyprint">
class User extends Model
{
    public function profile(): Relation
    {
        # profiles.user_id = users.id
        return $this->hasOne(Profile::class);
    }
}

echo $user->profile->city;
</pre>

<p>
    В обратной связи ссылка лежит в текущей таблице, и ключи указываются явно. Например, автор статьи — у статьи есть
    колонка <code>user_id</code>:
</p>
<pre class="prettyprint">
class Story extends Model
{
    public function user(): Relation
    {
        # users.id = stories.user_id
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

echo $story->user->login;
</pre>

<div class="alert alert-warning">
    Ключи по умолчанию у <code>hasOne</code> работают с Motor ORM 6.0.2. В 6.0.1 и раньше связь без ключей искала
    в связанной таблице колонку с её же именем (<code>profiles.profile_id</code>) и падала. На этих версиях
    указывайте ключи явно: <code>hasOne(Profile::class, 'user_id')</code>.
</div>

<p>
    Если связанной строки нет, <code>hasOne</code> возвращает пустую модель, а не <code>null</code>. Обращение к её
    колонке безопасно и вернёт <code>null</code>, поэтому <code>$story->user->login</code> не упадёт у статьи
    удалённого пользователя.
</p>

<h3 id="has-many">Один ко многим (hasMany)</h3>
<p>Возвращает <a href="/docs/orm-collection">коллекцию</a> моделей. Например, комментарии статьи, у каждого есть колонка <code>story_id</code>:</p>
<pre class="prettyprint">
class Story extends Model
{
    public function comments(): Relation
    {
        # comments.story_id = stories.id
        return $this->hasMany(Comment::class);
    }
}

foreach ($story->comments as $comment) {
    echo $comment->text;
}

echo $story->comments->count();
</pre>

<p>Если связанных строк нет, возвращается пустая коллекция.</p>

<h3 id="has-many-through">Многие ко многим (hasManyThrough)</h3>
<p>
    Связь через промежуточную таблицу. Например, статьи и теги, где таблица <code>tag_story</code> хранит пары
    <code>story_id</code> и <code>tag_id</code>:
</p>
<pre class="prettyprint">
class Story extends Model
{
    public function tags(): Relation
    {
        return $this->hasManyThrough(Tag::class, TagStory::class);
    }
}

$story->tags->pluck('name');
</pre>

<p>
    Первые два аргумента — конечная и промежуточная модели. Если колонки названы не по правилам, дальше по порядку
    передаются ключи:
</p>
<ol>
    <li>колонка промежуточной таблицы со ссылкой на текущую модель — <code>story_id</code>;</li>
    <li>колонка промежуточной таблицы со ссылкой на конечную модель — <code>tag_id</code>;</li>
    <li>колонка текущей таблицы — <code>stories.id</code>;</li>
    <li>колонка конечной таблицы — <code>tags.id</code>.</li>
</ol>

<h3 id="constrain">Постоянные условия связи (constrain)</h3>
<p>
    <code>constrain()</code> добавляет к связи условия, с которыми она загружается всегда. Замыкание получает запрос
    к связанной таблице. Так в Motor CMS устроена отметка «в избранном» у статьи — среди всех отметок нужна только
    отметка текущего пользователя:
</p>
<pre class="prettyprint">
class Story extends Model
{
    public function favorite(): Relation
    {
        return $this->hasOne(Favorite::class, 'story_id')->constrain(
            static fn (Query $query) => $query->where('user_id', getUser('id'))
        );
    }
}

if ($story->favorite->id) {
    echo 'В избранном';
}
</pre>

<p>Условия могут включать и сортировку:</p>
<pre class="prettyprint">
public function approvedComments(): Relation
{
    return $this->hasMany(Comment::class)->constrain(
        static fn (Query $query) => $query->where('approved', 1)->orderByDesc('id')
    );
}
</pre>

<div class="alert alert-warning">
    <code>limit()</code> и <code>offset()</code> внутри <code>constrain()</code> относятся ко всему чтению, а не к каждой
    записи. Связь читается одним запросом сразу для всех записей выборки, поэтому <code>limit(1)</code> вернёт одну
    строку на весь результат, а не «последний комментарий у каждой статьи». Для такой задачи отсортируйте связь и
    возьмите первый элемент коллекции.
</div>

<h3 id="loading">Как загружаются связи</h3>
<p>Связь читается при первом обращении к ней и запоминается на записи: повторное обращение не читает файл заново.</p>

<p>
    Главное правило: <strong>связь, затронутая на одной записи, загружается сразу для всех записей той же выборки</strong>.
    Проблемы «N + 1 запросов» нет, даже если о ней не думать:
</p>
<pre class="prettyprint">
foreach (Story::query()->limit(10)->get() as $story) {
    echo $story->user->login;
}
</pre>

<p>
    Первое обращение к <code>$story->user</code> читает авторов всех десяти статей за один проход по файлу пользователей,
    остальные девять итераций берут их из памяти. Записи, полученные по одной через <code>find()</code> или
    <code>first()</code>, объединять не с кем, поэтому они загружают только свою связь.
</p>

<p>Проверить, загружена ли связь, можно через <code>relationLoaded()</code>:</p>
<pre class="prettyprint">
$story->relationLoaded('user'); // false
$story->user;
$story->relationLoaded('user'); // true
</pre>

<h3 id="with">Загрузка вместе с запросом (with)</h3>
<p><code>with()</code> загружает связи сразу при выполнении запроса, а не при первом обращении:</p>
<pre class="prettyprint">
Story::query()
    ->orderByDesc('created_at')
    ->with(['user', 'comments'])
    ->paginate(10);
</pre>

<p>
    Быстрее от этого не станет: число проходов по файлам то же самое. <code>with()</code> нужен, когда результат
    передаётся коду, который не должен обращаться к файлам, например в шаблон, или чтобы явно показать, ради каких
    связей делается запрос. Работает с <code>get()</code>, <code>paginate()</code>, <code>first()</code> и
    <code>find()</code>.
</p>

<p>Связь можно сузить прямо в <code>with()</code>: имя связи становится ключом, а замыкание с условиями — значением.</p>
<pre class="prettyprint">
Story::query()
    ->with([
        'user',
        'comments' => static fn (Query $query) => $query->where('approved', 1),
    ])
    ->get();
</pre>

<p>
    Разница с <code>constrain()</code> в том, где живёт условие. <code>constrain()</code> описывает связь, которая
    <strong>всегда</strong> такая, в том числе при обращении без <code>with()</code>. Замыкание в <code>with()</code>
    сужает <strong>одно</strong> чтение. Если заданы оба, условия складываются.
</p>
