<?php

use App\Models\Story;
use MotorORM\Pagination;

/** @var string $tag */
/** @var Pagination|Story[] $stories */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?><?= $tag ?> (Стр. <?= $stories->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('description') ?><?= $tag ?> (Стр. <?= $stories->currentPage() ?>)<?php $this->stop() ?>

<?php $this->start('header') ?>
    <h2 class="page-title">Поиск по тегу: <?= $tag ?></h2>
<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item"><a href="<?= route('stories') ?>">Статьи</a></li>
        <li class="breadcrumb-item active">Поиск по тегу</li>
    </ol>
</nav>
<?php $this->stop() ?>

<?= $this->fetch('stories/_list', compact('stories')) ?>
