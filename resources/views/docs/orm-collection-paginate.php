<?php $this->layout('layout-docs') ?>

<?php $this->start('title') ?>Builder<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('docs') ?>">Документация</a></li>
            <li class="breadcrumb-item active">Пагинация коллекций</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<h3>Пагинация коллекций</h3>
<div class="mb-3">
    Пагинация коллекций - это методы организации длинных списков или наборов данных на веб-страницах, который позволяет пользователям просматривать содержимое по частям. Вместо того, чтобы загружать все элементы одновременно, пагинация разбивает их на отдельные страницы с фиксированным количеством элементов на каждой странице.
</div>
<div class="mb-3">
    Этот подход имеет несколько преимуществ. Во-первых, он ускоряет загрузку страницы и уменьшает нагрузку на сервер, поскольку только необходимые данные загружаются по мере необходимости. Во-вторых, он облегчает навигацию по длинным спискам, позволяя пользователям быстро перемещаться между страницами.
</div>
<div class="mb-3">
    Кроме того, пагинация позволяет различным пользователям просматривать определенные страницы одновременно, что может быть полезно при работе с большими объемами данных. Например, это может быть удобно для разделения контента на страницы блога или списка товаров в интернет-магазине.
</div>

<div class="mb-3">
    Класс CollectionPaginate наследует класс Collection и расширяет его функциональность
</div>

<ul>
    <li><a href="#setting">Настройка пагинатора</a></li>
    <li><a href="#current-page">Получение текущей страницы (currentPage)</a></li>
    <li><a href="#total">Получение количества элементов (total)</a></li>
    <li><a href="#links">Получение сформированного кода страниц (links)</a></li>
    <li><a href="#with-path">Установка url страниц (withPath)</a></li>
    <li><a href="#appends">Установка параметров страниц (appends)</a></li>
</ul>

<h3 id="setting">Настройки пагинатора</h3>
Некоторые параметры пагинатора можно переопределить<br>

<pre class="prettyprint">
&lt;?php

namespace App\Models;

use MotorORM\Builder;

class Model extends Builder
{
    // Переопределение имени страниц
    protected ?string $paginateName = 'page';

    // Переопределение базового шаблона со страницами
    protected ?string $paginateView = __DIR__ . '/../../resources/views/app/_paginator.php';
}
</pre>

<h3 id="current-page">Получение текущей страницы (currentPage)</h3>
Метод currentPage() возвращает текущую страницу:

<pre class="prettyprint">
$pages = Model::query()->paginate(10);
$pages->currentPage();

// 1
</pre>

<h3 id="total">Получение количества элементов (total)</h3>
Метод total() возвращает общее количество элементов:

<pre class="prettyprint">
$pages = Model::query()->paginate(10);
$pages->total();

// 12
</pre>

<h3 id="links">Получение сформированного кода страниц (links)</h3>
Метод links() возвращает сформированный блок html со списком страниц:

<pre class="prettyprint">
$pages = Model::query()->paginate(10);
$pages->links();

/*
&lt;nav>
    &lt;ul class="pagination">
        &lt;li class="page-item active">&lt;span class="page-link">1&lt;/span>&lt;/li>
        &lt;li class="page-item">&lt;a class="page-link" href="?page=2">2&lt;a>&lt;li>
        &lt;li class="page-item">&lt;a class="page-link" href="?page=2">»&lt;a>&lt;li>
    &lt;/ul>
&lt;/nav>
*/
</pre>

<h3 id="with-path">Установка url страниц (withPath)</h3>
По умолчанию ссылки, сгенерированные пагинатором, будут соответствовать URI текущего запроса.<br>
Однако метод пагинатора withPath() позволяет настроить URI, используемый пагинатором при создании ссылок.

<pre class="prettyprint">
$pages = Model::query()->paginate(10);

$pages->withPath('/admin/users');

// /admin/users?page=N
</pre>

<h3 id="appends">Установка параметров страниц (appends)</h3>
Вы можете добавить к ссылке на страницы дополнительные параметры, используя метод appends().

<pre class="prettyprint">
$pages = Model::query()->paginate(10);

$pages->appends(['sort' => 'votes']);

// /admin/users?page=N&amp;sort=votes
</pre>
