<?php $this->layout('layout-docs') ?>

<?php $this->start('title') ?>Запросы<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('docs') ?>">Документация</a></li>
            <li class="breadcrumb-item active">Запросы</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<p>
    Запрос начинается с <code>Model::query()</code>. К нему по цепочке добавляются условия, сортировка и лимит,
    а последний метод — <code>get()</code>, <code>first()</code>, <code>count()</code>, <code>update()</code> и другие —
    выполняет его.
</p>

<ul>
    <li><a href="#read">Чтение</a></li>
    <li><a href="#find">Поиск по ключу</a></li>
    <li><a href="#where">Условия</a></li>
    <li><a href="#like">Поиск по шаблону (like)</a></li>
    <li><a href="#order">Сортировка, лимит и смещение</a></li>
    <li><a href="#cursor">Обход большой таблицы</a></li>
    <li><a href="#write">Запись</a></li>
    <li><a href="#when">Условия по необходимости (when)</a></li>
</ul>

<h3 id="read">Чтение</h3>
<pre class="prettyprint">
# По ключу: модель или null
Story::query()->find(1);

# Первое совпадение: модель или null
Story::query()->where('user_id', 5)->first();

# Все совпадения: коллекция, пустая если ничего не нашлось
Story::query()->where('user_id', 5)->get();

# Есть ли хоть одно совпадение
Story::query()->where('user_id', 5)->exists();

# Сколько записей подходит
Story::query()->where('active', 1)->count();

# Названия колонок файла
Story::query()->headers();
</pre>

<p>
    <code>first()</code> и <code>exists()</code> прекращают чтение на первом совпадении, поэтому запись в начале
    файла находят почти мгновенно. Что умеет прочитанная модель, описано в разделе
    <a href="/docs/orm-models#row">Модель как строка</a>, а что умеет коллекция — в разделе
    <a href="/docs/orm-collection">Коллекции</a>.
</p>

<h3 id="find">Поиск по ключу</h3>
<pre class="prettyprint">
Story::query()->find(1);

# Строковый ключ
Setting::query()->find('theme');
</pre>

<p>
    <code>find()</code> не читает таблицу построчно. Ключи выдаются по возрастанию, поэтому файл обычно отсортирован
    по первой колонке, и нужную строку можно найти делением файла пополам. На таблице в 50 000 строк это 2 мс
    вместо 73.
</p>

<p>
    Если файл не отсортирован, ключ нечисловой или строка повреждена, <code>find()</code> молча переходит на обычный
    проход по файлу. Ответ при этом всегда верный, меняется только скорость.
</p>

<p>Деление пополам работает только у «голого» <code>find()</code>. Условия или сортировка перед ним означают полный проход:</p>
<pre class="prettyprint">
Story::query()->find(1);                        // деление пополам
Story::query()->where('active', 1)->find(1);    // полный проход
</pre>

<h3 id="where">Условия</h3>
<pre class="prettyprint">
# Равенство
Story::query()->where('user_id', 5)->get();

# Сравнение: = != &lt;&gt; &gt; &gt;= &lt; &lt;=
Story::query()->where('rating', '&gt;=', 10)->get();

# Несколько условий объединяются через «и»
Story::query()->where('active', 1)->where('rating', '&gt;', 0)->get();

# Или
Story::query()->where('id', 1)->orWhere('id', 2)->get();

# Значение из списка и не из списка
Story::query()->whereIn('id', [1, 3, 4, 7])->get();
Story::query()->whereNotIn('user_id', [2, 3])->get();
</pre>

<p>Замыкание группирует условия, как скобки в SQL. Группы можно вкладывать друг в друга:</p>
<pre class="prettyprint">
# active = 1 AND (rating &gt; 10 OR user_id = 1)
Story::query()
    ->where('active', 1)
    ->where(function (Query $query) {
        $query->where('rating', '&gt;', 10)->orWhere('user_id', 1);
    })
    ->get();
</pre>

<p>
    Условие по колонке, которой нет в файле, бросает <code>UnexpectedValueException</code>, а неизвестный оператор —
    <code>InvalidArgumentException</code>. Опечатка падает сразу, а не возвращает пустой результат.
</p>

<h3 id="like">Поиск по шаблону (like)</h3>
<p>Знак <code>%</code> означает «любые символы в этом месте»:</p>
<pre class="prettyprint">
# Начинается на motor
Story::query()->whereLike('title', 'motor%')->get();

# Заканчивается на motor
Story::query()->whereLike('title', '%motor')->get();

# Содержит motor
Story::query()->whereLike('title', '%motor%')->get();

# Не содержит motor
Story::query()->whereNotLike('title', '%motor%')->get();

# Через «или»
Story::query()->where('id', 1)->orWhereLike('title', '%motor%')->get();
Story::query()->where('id', 1)->orWhereNotLike('title', '%motor%')->get();
</pre>

<p>
    Шаблон без <code>%</code> сравнивается со всем значением целиком, как <code>LIKE</code> в SQL:
    <code>whereLike('title', 'motor')</code> найдёт только «motor».
</p>

<p>Регистр по умолчанию не учитывается. Параметр <code>caseSensitive</code> включает точное сравнение:</p>
<pre class="prettyprint">
# Найдёт admin, Admin, ADMIN
User::query()->whereLike('login', 'admin')->first();

# Только admin
User::query()->whereLike('login', 'admin', caseSensitive: true)->first();
</pre>

<div class="alert alert-warning">
    В версиях до 6.0 поиск по шаблону записывался как <code>where('title', 'like', '%motor%')</code>, а без учёта
    регистра — как <code>where('login', 'lax', 'admin')</code>. Оба варианта больше не работают: используйте
    <code>whereLike()</code>.
</div>

<h3 id="order">Сортировка, лимит и смещение</h3>
<pre class="prettyprint">
# По возрастанию
Story::query()->orderBy('created_at')->get();

# По убыванию
Story::query()->orderByDesc('created_at')->get();

# По нескольким колонкам, в порядке добавления
Story::query()
    ->orderByDesc('rating')
    ->orderBy('id')
    ->get();

# Записи с 11-й по 20-ю
Story::query()->offset(10)->limit(10)->get();
</pre>

<p>
    Направление сортировки — перечисление <code>SortOrder</code>. Если оно приходит от пользователя, проверьте его
    через <code>tryFrom()</code>: неизвестное значение станет <code>null</code>, и сработает значение по умолчанию.
</p>
<pre class="prettyprint">
$sort = SortOrder::tryFrom($_GET['sort'] ?? '') ?? SortOrder::Desc;

Story::query()->orderBy('created_at', $sort)->paginate(10);
</pre>

<p>
    Сортировка держит подходящие строки в памяти, поэтому на большом файле сначала сузьте выборку через
    <code>where()</code>. С <code>limit()</code> в памяти остаются только строки, которые попадут в ответ.
</p>

<h3 id="cursor">Обход большой таблицы</h3>
<p>
    <code>get()</code> собирает все найденные записи в коллекцию и только потом отдаёт её. <code>cursor()</code>
    отдаёт записи по одной, и в памяти живёт только текущая:
</p>
<pre class="prettyprint">
foreach (Story::query()->where('active', 1)->cursor() as $story) {
    echo $story->title;
}
</pre>

<p>
    На таблице в 50 000 строк это 0 МБ памяти против 33 МБ у <code>get()</code> при той же скорости. Подходит
    для экспорта, пересчёта и других фоновых задач.
</p>

<p>
    У курсора есть ограничение: записи не накапливаются, поэтому связи нельзя загрузить пачкой. Обращение к связи
    внутри цикла читает связанную таблицу на каждой записи. Если в цикле нужны связи, используйте <code>get()</code>.
</p>

<h3 id="write">Запись</h3>
<pre class="prettyprint">
# Вставка: числовой ключ будет выдан автоматически
Story::query()->create(['title' => 'Привет', 'user_id' => 1]);

# Вставка со строковым ключом
Setting::query()->create(['name' => 'theme', 'value' => 'dark']);

# Изменить все подходящие строки, вернёт число изменённых
Story::query()->where('user_id', 5)->update(['active' => 0]);

# Удалить все подходящие строки, вернёт число удалённых
Story::query()->where('active', 0)->delete();

# Очистить таблицу, сохранив заголовки
Story::query()->truncate();
</pre>

<p>
    <code>create()</code> возвращает созданную модель. Если ключ уже занят или строковый ключ не передан,
    он бросает <code>UnexpectedValueException</code>.
</p>

<p>Изменить или удалить одну уже прочитанную строку можно через саму модель:</p>
<pre class="prettyprint">
$story = Story::query()->find(17);

$story->update(['title' => 'Новый заголовок']);
$story->delete();
</pre>

<h3 id="when">Условия по необходимости (when)</h3>
<p>
    <code>when()</code> добавляет условия, только если первый аргумент истинный. Это избавляет от <code>if</code>
    вокруг необязательных фильтров:
</p>
<pre class="prettyprint">
$stories = Story::query()
    ->when($userId, function (Query $query, $userId) {
        $query->where('user_id', $userId);
    })
    ->paginate(10);
</pre>

<p>Третий аргумент выполняется, когда значение ложное:</p>
<pre class="prettyprint">
$stories = Story::query()
    ->when(
        $popular,
        fn (Query $query) => $query->orderByDesc('rating'),
        fn (Query $query) => $query->orderByDesc('created_at'),
    )
    ->paginate(10);
</pre>
