<?php

use App\Models\Story;
use MotorORM\CollectionPaginate;

/** @var CollectionPaginate|Story[] $stories */
?>
<?php $this->layout('layout-docs2') ?>

<?php $this->start('title') ?>Все версии - Motor CMS<?php $this->stop() ?>
<?php $this->start('description') ?>Motor CMS - Легкий и быстрый движок для сайта. Не использует базу данных, не требует особых библиотек на сервере<?php $this->stop() ?>

<?php $this->start('header') ?>
    <h1>Все версии</h1>
<?php $this->stop() ?>

В разработке
