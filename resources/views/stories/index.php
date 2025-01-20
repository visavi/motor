<?php

use App\Services\Pagination;

/** @var Pagination $pagination */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Статьи (Стр. <?= $pagination->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('description') ?>Статьи (Стр. <?= $pagination->currentPage() ?>)<?php $this->stop() ?>

<?php $this->start('header') ?>
    <h1><?= setting('main.title') ?></h1>
<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item">Статьи</li>
    </ol>
</nav>
<?php $this->stop() ?>

<?= $this->fetch('stories/_list', compact('pagination')) ?>
