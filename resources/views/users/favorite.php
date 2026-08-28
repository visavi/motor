<?php

use App\Models\Story;
use App\Models\User;
use MotorORM\Pagination;

/** @var User $user */
/** @var Pagination|Story[] $stories */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Избранные статьи (Стр. <?= $stories->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('description') ?>Избранные статьи (Стр. <?= $stories->currentPage() ?>)<?php $this->stop() ?>

<?php $this->start('header') ?>
    <h2 class="page-title">Избранные статьи</h2>
<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item active">Избранные статьи</li>
    </ol>
</nav>
<?php $this->stop() ?>

<?= $this->fetch('stories/_list', compact('stories') + [
    'emptyTitle'    => 'В избранном пусто',
    'emptySubtitle' => 'Отмечайте статьи сердечком, и они соберутся здесь',
]) ?>
