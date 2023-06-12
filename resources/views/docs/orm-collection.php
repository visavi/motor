<?php $this->layout('layout-docs') ?>

<?php $this->start('title') ?>Builder<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('docs') ?>">Документация</a></li>
            <li class="breadcrumb-item active">Коллекции</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<h3>Коллекции</h3>
<div class="mb-3">
    Коллекции - это набор инструментов для работы с массивами и коллекциями данных.<br>
    Коллекции позволяет работать с данными более эффективно и удобно, чем стандартные функции PHP.
</div>
<div class="mb-3">
    Коллекции предоставляет множество методов для работы с коллекциями данных, таких как сортировка, фильтрация, группировка, поиск, преобразование и т.д.<br>
    Эти методы позволяют быстро и легко получать нужные данные из больших массивов и коллекций.
</div>
<div class="mb-3">
    Кроме того, коллекции поддерживает цепочку методов, которая позволяет выполнять несколько операций над коллекцией данных одновременно. Это значительно упрощает код и повышает его читаемость.
</div>

<ul>
    <li><a href="#all">Преобразование коллекции в массив (all)</a></li>
    <li><a href="#get">Получение элемента по ключу (get)</a></li>
    <li><a href="#first">Получение первой записи (first)</a></li>
    <li><a href="#last">Получение последней записи (last)</a></li>
    <li><a href="#pull">Удаление и получение записи из коллекции (pull)</a></li>
    <li><a href="#forget">Удаление записи из коллекции (forget)</a></li>
    <li><a href="#keys">Получение всех ключей коллекции (keys)</a></li>
    <li><a href="#values">Получение всех значений коллекции (values)</a></li>
    <li><a href="#has">Проверка ключа на существование (has)</a></li>
    <li><a href="#contains">Проверка значение на существование (contains)</a></li>
    <li><a href="#search">Поиск значения в коллекции (search)</a></li>
    <li><a href="#count">Получение количества записей в коллекции (count)</a></li>
    <li><a href="#put">Установка значения в коллекции (put)</a></li>
    <li><a href="#push">Добавление записи в коллекцию (push)</a></li>
    <li><a href="#is-empty">Проверка коллекции на пустоту (isEmpty)</a></li>
    <li><a href="#is-not-empty">Проверка коллекции на заполненность (isNotEmpty)</a></li>
    <li><a href="#clear">Очистка коллекции (clear)</a></li>
    <li><a href="#slice">Срез коллекции (slice)</a></li>
    <li><a href="#pluck">Получение всех записей по ключу (pluck)</a></li>
    <li><a href="#filter">Фильтрация записей (filter)</a></li>
</ul>

<h3 id="all">Преобразование коллекции в массив (all)</h3>
Метод all() возвращает заданный массив, представленный коллекцией:

<pre class="prettyprint">
(new Collection([1, 2, 3]))->all();

// [1, 2, 3]
</pre>

<h3 id="get">Получение элемента по ключу (get)</h3>
Метод get() возвращает нужный элемент по заданному ключу. Если ключ не существует, то возвращается null:

<pre class="prettyprint">
$collection = new Collection(['name' => 'Vantuz', 'cms' => 'motor']);

$value = $collection->get('name');

// Vantuz
</pre>

Вторым параметром вы можете передать значение по умолчанию:

<pre class="prettyprint">
$collection = new Collection(['name' => 'Vantuz', 'cms' => 'motor']);

$value = $collection->get('foo', 'default-value');

// default-value
</pre>

<h3 id="first">Получение первой записи (first)</h3>
Метод first() возвращает первый элемент в коллекции, который проходит заданный тест на истинность:

<pre class="prettyprint">
(new Collection([1, 2, 3, 4]))->first(function (int $value, int $key) {
    return $value > 2;
});

// 3
</pre>

Вы также можете вызвать first() метод без аргументов, чтобы получить первый элемент в коллекции. Если коллекция пуста, то возвращается null:

<pre class="prettyprint">
(new Collection([1, 2, 3, 4]))->first();

// 1
</pre>

<h3 id="last">Получение последней записи (last)</h3>
Метод last() возвращает последний элемент в коллекции, который проходит заданный тест на истинность:

<pre class="prettyprint">
(new Collection([1, 2, 3, 4]))->last(function (int $value, int $key) {
    return $value < 3;
});

// 2
</pre>

Вы также можете вызвать last() метод без аргументов, чтобы получить последний элемент в коллекции. Если коллекция пуста, то возвращается null:

<pre class="prettyprint">
(new Collection([1, 2, 3, 4]))->last();

// 4
</pre>

<h3 id="pull">Удаление и получение записи из коллекции (pull)</h3>
Метод pull() удаляет и возвращает элемент из коллекции по его ключу:

<pre class="prettyprint">
$collection = new Collection(['product_id' => 'prod-100', 'name' => 'Desk']);

$collection->pull('name');

// 'Desk'

$collection->all();

// ['product_id' => 'prod-100']
</pre>

<h3 id="forget">Удаление записи из коллекции (forget)</h3>
Метод forget() удаляет элемент из коллекции по его ключу:

<pre class="prettyprint">
$collection = new Collection(['name' => 'Vantuz', 'cms' => 'motor']);

$collection->forget('name');

$collection->all();

// ['cms' => 'motor']
</pre>

<h3 id="keys">Получение всех ключей коллекции (keys)</h3>
Метод keys() возвращает все ключи коллекции:

<pre class="prettyprint">
$collection = new Collection([
  'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
  'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
]);

$keys = $collection->keys();

$keys->all();

// ['prod-100', 'prod-200']
</pre>

<h3 id="values">Получение всех значений коллекции (values)</h3>
Метод values() возвращает новую коллекцию со сброшенными ключами и последовательно пронумерованными индексами:

<pre class="prettyprint">
$collection = new Collection([
  10 => ['product' => 'Desk', 'price' => 200],
  11 => ['product' => 'Desk', 'price' => 200]
]);

$values = $collection->values();

$values->all();

/*[
    0 => ['product' => 'Desk', 'price' => 200],
    1 => ['product' => 'Desk', 'price' => 200],
]*/
</pre>

<h3 id="has">Проверка ключа на существование (has)</h3>
Метод has() определяет, существует ли заданный ключ в коллекции:

<pre class="prettyprint">
$collection = new Collection(['account_id' => 1, 'product' => 'Desk']);

$collection->has('product');

// true
</pre>

<h3 id="contains">Проверка значение на существование (contains)</h3>

Метод contains() определяет, содержит ли коллекция данный элемент. Вы можете передать методу замыкание contains(), чтобы определить, существует ли в коллекции элемент, соответствующий данному критерию истинности:

<pre class="prettyprint">
$collection = new Collection([1, 2, 3, 4, 5]);

$collection->contains(function (int $value, int $key) {
    return $value > 5;
});

/ false
</pre>

В качестве альтернативы вы можете передать строку методу contains(), чтобы определить, содержит ли коллекция заданное значение элемента:

<pre class="prettyprint">
$collection = new Collection(['name' => 'Desk', 'price' => 100]);

$collection->contains('Desk');

// true

$collection->contains('New York');

// false
</pre>

<h3 id="search">Поиск значения в коллекции (search)</h3>
Метод search() ищет в коллекции заданное значение и возвращает его ключ при успешном поиске. Если элемент не найден, то возвращается false.

<pre class="prettyprint">
$collection = new Collection([2, 4, 6, 8]);

$collection->search(4);

// 1
</pre>
Поиск проводится с помощью «неточного» сравнения, то есть строка с числовым значением будет считаться равной числу с таким же значением. Чтобы использовать строгое сравнение, передайте true вторым параметром метода:

<pre class="prettyprint">
$collection->search('4', true);

// false
</pre>

В качестве альтернативы вы можете передать собственное замыкание для поиска первого элемента, который проходит указанный тест на истинность:
<pre class="prettyprint">
$collection->search(function ($item, $key) {
    return $item > 5;
});

// 2
</pre>

<h3 id="count">Получение количества записей в коллекции (count)</h3>
Метод count() возвращает общее количество элементов в коллекции:

<pre class="prettyprint">
$collection = new Collection([1, 2, 3, 4]);

$collection->count();

// 4
</pre>

<h3 id="put">Установка значения в коллекции (put)</h3>
Метод put() устанавливает заданный ключ и значение в коллекцию:

<pre class="prettyprint">
$collection = new Collection(['product_id' => 1, 'name' => 'Desk']);

$collection->put('price', 100);
$collection->all();

// ['product_id' => 1, 'name' => 'Desk', 'price' => 100]
</pre>

<h3 id="push">Добавление записи в коллекцию (push)</h3>
Метод push() добавляет элемент в конец коллекции:

<pre class="prettyprint">
$collection = new Collection([1, 2, 3, 4]);

$collection->push(5);
$collection->all();

// [1, 2, 3, 4, 5]
</pre>

<h3 id="is-empty">Проверка коллекции на пустоту (isEmpty)</h3>
Метод isEmpty() возвращает true, если коллекция пуста. В противном случае вернётся false:

<pre class="prettyprint">
$collection = new Collection([]);
$collection->isEmpty();

// true
</pre>

<h3 id="is-not-empty">Проверка коллекции на заполненность (isNotEmpty)</h3>
Метод isNotEmpty() возвращает true, если коллекция не пустая. В противном случае вернётся false:

<pre class="prettyprint">
$collection = new Collection([1, 2, 3]);
$collection->isNotEmpty();

// true
</pre>

<h3 id="clear">Очистка коллекции (clear)</h3>
<pre class="prettyprint">
$collection = new Collection([1, 2, 3]);
$slice = $collection->clear();

// []
</pre>

<h3 id="slice">Срез коллекции (slice)</h3>
Метод slice() возвращает часть коллекции, начиная с заданного индекса:

<pre class="prettyprint">
$collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
$slice = $collection->slice(4);
$slice->all();

// [5, 6, 7, 8, 9, 10]
</pre>

Если вы хотите ограничить размер получаемой части коллекции, передайте желаемый размер вторым параметром в метод:
<pre class="prettyprint">
$slice = $collection->slice(4, 2);
$slice->all();

// [5, 6]
</pre>

<h3 id="pluck">Получение всех записей по ключу (pluck)</h3>
Метод pluck() извлекает все значения по заданному ключу:

<pre class="prettyprint">
 $collection = new Collection([
    ['login' => 'Alex', 'name' => 'Саня'],
    ['login' => 'Viktor', 'name' => 'Виктор'],
]);

// ['Саня', 'Виктор']
</pre>

Также вы можете указать, с каким ключом вы хотите получить коллекцию:
<pre class="prettyprint">
$collection->pluck('name', 'login');
$collection->all();

//['Alex' => 'Саня', 'Viktor' => 'Виктор']
</pre>

<h3 id="filter">Фильтрация записей (filter)</h3>
Метод filter() фильтрует коллекцию, используя callback-функции, eсли callback-функция возвращает true, данное значение возвращается в результирующую коллекцию:

<pre class="prettyprint">
$collection = new Collection([1, 2, 3, 4]);

$filtered = $collection->filter(function (int $value, int $key) {
    return $value > 2;
});

$filtered->all();

// [3, 4]
</pre>

Если callback-функция не указана все пустые значения массива array будут удалены
<pre class="prettyprint">
$collection = new Collection([1, 2, 3, null, false, '', 0, []]);

$collection->filter()->all();

// [1, 2, 3]
</pre>
