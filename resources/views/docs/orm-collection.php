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

<ul>
    <li><a href="#">Collection</a></li>
    <li><a href="#">Преобразование коллекции в массив</a></li>
    <li><a href="#">Получение первой записи</a></li>
    <li><a href="#">Получение последней записи</a></li>
    <li><a href="#">Получение количества записей в коллекции</a></li>
    <li><a href="#">Добавление записи в коллекцию</a></li>
    <li><a href="#">Удаление записи из коллекции</a></li>
    <li><a href="#">Установка значения в коллекции</a></li>
    <li><a href="#">Проверка коллекции на пустоту</a></li>
    <li><a href="#">Очистка коллекции</a></li>
    <li><a href="#">Срез коллекции</a></li>
    <li><a href="#">Обход с получением ключа и значения из коллекции</a></li>
    <li><a href="#">Collection Paginate</a></li>
    <li><a href="#">Получение текущей страницы</a></li>
    <li><a href="#">Получение количества страниц</a></li>
    <li><a href="#">Получение массива со страницами</a></li>
</ul>
