<?php $this->layout('layout-docs') ?>

<?php $this->start('title') ?>Пагинация<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('docs') ?>">Документация</a></li>
            <li class="breadcrumb-item active">Пагинация</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<p>
    Пагинация делит длинный список на страницы. Таблица при этом не загружается в память целиком: читаются только
    строки текущей страницы, поэтому <code>paginate()</code> одинаково быстро работает и на сотне строк, и на сотнях тысяч.
</p>

<ul>
    <li><a href="#paginate">Постраничный вывод</a></li>
    <li><a href="#info">Сведения о странице</a></li>
    <li><a href="#links">Ссылки на страницы</a></li>
    <li><a href="#render">Вывод навигации</a></li>
    <li><a href="#page-source">Откуда берётся номер страницы</a></li>
    <li><a href="#simple">Пагинация без подсчёта</a></li>
</ul>

<h3 id="paginate">Постраничный вывод</h3>
<p><code>paginate()</code> принимает число записей на странице и завершает запрос вместо <code>get()</code>:</p>
<pre class="prettyprint">
$stories = Story::query()
    ->active()
    ->orderByDesc('created_at')
    ->paginate(10);

foreach ($stories as $story) {
    echo $story->title;
}

echo pagination($stories);
</pre>

<p>
    Результат — объект <code>Pagination</code>. Это <a href="/docs/orm-collection">коллекция</a> записей текущей
    страницы, которая дополнительно знает, где эта страница стоит среди остальных. <code>pagination()</code> —
    хелпер Motor CMS, выводящий навигацию по страницам.
</p>

<p>Номер за пределами диапазона приводится к ближайшему, поэтому <code>?page=9999</code> покажет последнюю страницу, а не пустой список.</p>

<h3 id="info">Сведения о странице</h3>
<pre class="prettyprint">
$stories->currentPage();    // номер текущей страницы
$stories->lastPage();       // номер последней страницы
$stories->perPage();        // записей на странице
$stories->total();          // записей всего

$stories->firstItem();      // порядковый номер первой записи на странице
$stories->lastItem();       // порядковый номер последней

$stories->hasPages();       // больше одной страницы
$stories->hasMorePages();   // есть ли страница после текущей
$stories->onFirstPage();
$stories->onLastPage();
</pre>

<p>Например, подпись под списком:</p>
<pre class="prettyprint">
printf('Показаны %d–%d из %d', $stories->firstItem(), $stories->lastItem(), $stories->total());
// Показаны 11–20 из 45
</pre>

<p>Если ничего не нашлось, <code>firstItem()</code> и <code>lastItem()</code> возвращают <code>null</code>.</p>

<h3 id="links">Ссылки на страницы</h3>
<p>
    По умолчанию ссылки относительные — <code>?page=2</code> — и ведут на текущий адрес. Ссылка на первую страницу
    не содержит номера, чтобы у одной страницы не было двух адресов.
</p>

<p><code>withPath()</code> задаёт адрес, на который ведут ссылки:</p>
<pre class="prettyprint">
$users = User::query()->paginate(20)->withPath('/users');
// /users?page=2
</pre>

<p><code>appends()</code> добавляет к ссылкам параметры, например поисковый запрос или сортировку:</p>
<pre class="prettyprint">
$stories = Story::query()
    ->whereLike('title', '%' . $search . '%')
    ->paginate(10)
    ->appends(['search' => $search]);
// ?search=motor&amp;page=2
</pre>

<p><code>url()</code> возвращает адрес любой страницы, в том числе той, которой нет в навигации:</p>
<pre class="prettyprint">
$stories->url($stories->lastPage());
</pre>

<p><code>onEachSide()</code> задаёт, сколько номеров показывать по обе стороны от текущего. По умолчанию — один.</p>
<pre class="prettyprint">
$stories->onEachSide(3);
</pre>

<h3 id="render">Вывод навигации</h3>
<p>
    ORM не выводит HTML. <code>pages()</code> возвращает навигацию как массив объектов <code>Page</code>, а разметку
    строит приложение. У каждого объекта есть свойства:
</p>

<div class="table-responsive mb-3">
    <table class="table table-vcenter">
        <thead>
            <tr>
                <th>Свойство</th>
                <th>Значение</th>
            </tr>
        </thead>
        <tbody>
            <tr><td><code>name</code></td><td>что печатать: номер страницы или стрелку</td></tr>
            <tr><td><code>url</code></td><td>адрес ссылки; <code>null</code> у текущей страницы и у разделителя</td></tr>
            <tr><td><code>number</code></td><td>номер страницы, на которую ведёт ссылка</td></tr>
            <tr><td><code>current</code></td><td>это текущая страница</td></tr>
            <tr><td><code>separator</code></td><td>это разделитель «…» на месте пропущенных страниц</td></tr>
        </tbody>
    </table>
</div>

<p>В Motor CMS навигацию выводит шаблон <code>resources/views/app/_paginator.php</code>, его и вызывает хелпер <code>pagination()</code>. Упрощённо он выглядит так:</p>
<pre class="prettyprint">
&lt;ul class="pagination"&gt;
    &lt;?php foreach ($pages as $page): ?&gt;
        &lt;?php if ($page-&gt;separator): ?&gt;
            &lt;li class="page-item disabled"&gt;&lt;span class="page-link"&gt;…&lt;/span&gt;&lt;/li&gt;
        &lt;?php elseif ($page-&gt;current): ?&gt;
            &lt;li class="page-item active"&gt;&lt;span class="page-link"&gt;&lt;?= $page-&gt;name ?&gt;&lt;/span&gt;&lt;/li&gt;
        &lt;?php else: ?&gt;
            &lt;li class="page-item"&gt;
                &lt;a class="page-link" href="&lt;?= escape($page-&gt;url) ?&gt;"&gt;&lt;?= escape((string) $page-&gt;name) ?&gt;&lt;/a&gt;
            &lt;/li&gt;
        &lt;?php endif; ?&gt;
    &lt;?php endforeach; ?&gt;
&lt;/ul&gt;
</pre>

<p>
    <code>url</code> — обычная строка, и в ней могут оказаться параметры из <code>appends()</code>, пришедшие от
    пользователя. Экранируйте её при выводе, как любые другие данные.
</p>

<h3 id="page-source">Откуда берётся номер страницы</h3>
<p>
    ORM не читает <code>$_GET</code> сама. Номер страницы задаёт приложение, а если не задало, всегда показывается первая.
    Motor CMS делает это один раз при запуске, в <code>app/Factories/AppFactory.php</code>:
</p>
<pre class="prettyprint">
Pagination::resolvePageUsing(static fn (string $name) => $_GET[$name] ?? 1);
</pre>

<p>
    Замыкание получает имя параметра и возвращает номер. Значение, которое не является числом, считается первой
    страницей. Явно задать страницу для одного запроса можно через <code>page()</code>:
</p>
<pre class="prettyprint">
Story::query()->page(3)->paginate(10);
</pre>

<p>Имя параметра одно на всё приложение и по умолчанию равно <code>page</code>:</p>
<pre class="prettyprint">
Pagination::setPageName('p');   // ?p=3
</pre>

<div class="alert alert-warning">
    В версиях до 6.0 номер страницы брался из <code>$_GET</code> автоматически, навигацию печатал метод
    <code>links()</code>, а шаблон задавался свойством <code>$paginateView</code> модели. Всего этого больше нет:
    используйте <code>resolvePageUsing()</code> и <code>pages()</code>.
</div>

<h3 id="simple">Пагинация без подсчёта</h3>
<p>
    Чтобы показать номера всех страниц, нужно знать общее число записей, а для этого пройти весь файл. Если
    достаточно стрелок «назад» и «вперёд», <code>simplePaginate()</code> обходится без подсчёта: он читает на одну
    строку больше, чем нужно, и по ней понимает, есть ли следующая страница.
</p>
<pre class="prettyprint">
$stories = Story::query()->orderByDesc('id')->simplePaginate(10);

echo pagination($stories);
</pre>

<div class="table-responsive mb-3">
    <table class="table table-vcenter">
        <thead>
            <tr>
                <th>50 000 строк</th>
                <th class="text-end">первая страница</th>
                <th class="text-end">страница 4 900</th>
            </tr>
        </thead>
        <tbody>
            <tr><td><code>paginate(10)</code></td><td class="text-end">7.75 мс</td><td class="text-end">60.05 мс</td></tr>
            <tr><td><code>simplePaginate(10)</code></td><td class="text-end">1.45 мс</td><td class="text-end">54.09 мс</td></tr>
        </tbody>
    </table>
</div>

<p>
    Выигрыш заметен на первых страницах, на дальних его почти нет: чтение до нужного места всё равно стоит прохода
    по файлу. У <code>SimplePagination</code> нет <code>total()</code> и <code>lastPage()</code>, остальные методы те же.
</p>
