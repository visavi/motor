<?php $this->layout('layout-docs') ?>

<?php $this->start('title') ?>Коллекции<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('docs') ?>">Документация</a></li>
            <li class="breadcrumb-item active">Коллекции</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<p>
    <code>get()</code> и связи <code>hasMany</code> возвращают <code>Collection</code> — обёртку над массивом моделей.
    Её можно перебирать в <code>foreach</code>, передавать в <code>count()</code> и читать как массив:
</p>
<pre class="prettyprint">
$stories = Story::query()->where('active', 1)->get();

foreach ($stories as $story) {
    echo $story->title;
}

count($stories);
$stories[0]->title;
</pre>

<p>Коллекцию можно создать и из обычного массива:</p>
<pre class="prettyprint">
$collection = new Collection(['php', 'csv', 'orm']);
</pre>

<ul>
    <li><a href="#read">Получение элементов</a></li>
    <li><a href="#check">Проверки</a></li>
    <li><a href="#transform">Преобразования</a></li>
    <li><a href="#change">Изменение коллекции</a></li>
    <li><a href="#immutable">Новая коллекция или та же</a></li>
</ul>

<h3 id="read">Получение элементов</h3>
<pre class="prettyprint">
$stories->all();                  // исходный массив моделей
$stories->first();                // первый элемент или null
$stories->last();                 // последний элемент или null
$stories->get(5);                 // элемент по ключу или null
$stories->get(5, $default);       // элемент по ключу или значение по умолчанию
$stories->keys();                 // массив ключей
$stories->values();               // массив значений с ключами 0, 1, 2…
$stories->count();                // число элементов
</pre>

<p><code>first()</code> и <code>last()</code> принимают замыкание и тогда возвращают первое или последнее подходящее значение:</p>
<pre class="prettyprint">
$stories->first(fn (Story $story) => $story->rating &gt; 10);
$stories->last(fn (Story $story) => $story->locked);
</pre>

<h3 id="check">Проверки</h3>
<pre class="prettyprint">
$stories->isEmpty();
$stories->isNotEmpty();

# Есть ли ключ
$stories->has(0);

# Есть ли значение: обычное сравнение, строгое или через замыкание
$tags->contains('php');
$tags->contains('1', strict: true);
$stories->contains(fn (Story $story) => $story->user_id === 1);

# Ключ найденного значения или false
$tags->search('php');
$stories->search(fn (Story $story) => $story->rating &gt; 10);
</pre>

<p>В шаблонах удобно проверять коллекцию перед выводом:</p>
<pre class="prettyprint">
&lt;?php if ($stories-&gt;isNotEmpty()): ?&gt;
    &lt;?php foreach ($stories as $story): ?&gt;
        …
    &lt;?php endforeach; ?&gt;
&lt;?php else: ?&gt;
    Статей пока нет
&lt;?php endif; ?&gt;
</pre>

<h3 id="transform">Преобразования</h3>
<p>Эти методы возвращают новую коллекцию, а исходную не трогают.</p>

<p><code>pluck()</code> берёт одну колонку. Вторым аргументом можно указать колонку для ключей:</p>
<pre class="prettyprint">
$stories->pluck('title');               // ['Привет, мир', 'Вторая статья']
$stories->pluck('title', 'id');         // [1 => 'Привет, мир', 2 => 'Вторая статья']
$stories->pluck('title')->all();        // обычный массив
</pre>

<p><code>keyBy()</code> оставляет сами элементы, но меняет ключи на значения колонки или результат замыкания:</p>
<pre class="prettyprint">
$users = User::query()->get()->keyBy('id');
$users->get(5)->login;

$users->keyBy(fn (User $user) => strtolower($user->login));
</pre>

<p>
    При повторе ключа остаётся последний элемент, а элементы без такой колонки отбрасываются.
</p>

<p><code>filter()</code> оставляет элементы, для которых замыкание вернуло true. Без замыкания убирает пустые значения:</p>
<pre class="prettyprint">
$stories->filter(fn (Story $story) => $story->active);
$collection->filter();
</pre>

<p><code>slice()</code> вырезает часть коллекции, ключи при этом сохраняются:</p>
<pre class="prettyprint">
$stories->slice(0, 3);    // первые три
$stories->slice(5);       // всё начиная с шестого
</pre>

<h3 id="change">Изменение коллекции</h3>
<p>Эти методы меняют коллекцию, на которой вызваны:</p>
<pre class="prettyprint">
$collection->push('новое');       // добавить в конец
$collection->put('key', 'value'); // записать по ключу
$collection->pull('key');         // удалить по ключу и вернуть значение
$collection->forget('key');       // удалить по ключу
$collection->clear();             // удалить всё
</pre>

<p>Изменения касаются только коллекции в памяти. Чтобы удалить строки из файла, используйте <a href="/docs/orm-builder#write">запрос</a> или <code>$model->delete()</code>.</p>

<h3 id="immutable">Новая коллекция или та же</h3>
<p>
    <code>pluck()</code>, <code>keyBy()</code>, <code>filter()</code> и <code>slice()</code> возвращают новую коллекцию.
    Вызвать такой метод и не использовать результат — почти всегда ошибка, поэтому они помечены атрибутом
    <code>#[\NoDiscard]</code>, и PHP об этом предупредит:
</p>
<pre class="prettyprint">
$stories->filter(fn ($story) => $story->active);            // предупреждение: результат потерян
$active = $stories->filter(fn ($story) => $story->active);  // правильно
</pre>
