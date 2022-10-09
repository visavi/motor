<?php

use App\Models\Story;
use MotorORM\CollectionPaginate;

/** @var CollectionPaginate|Story[] $stories */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Статьи (Стр. <?= $stories->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('description') ?>Статьи (Стр. <?= $stories->currentPage() ?>)<?php $this->stop() ?>

<?php $this->start('header') ?>
    <h1><?= setting('main.title') ?></h1>
<?php $this->stop() ?>

<?= $this->fetch('stories/_list', compact('stories')) ?>
