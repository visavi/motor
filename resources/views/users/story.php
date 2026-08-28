<?php

use App\Models\Story;
use App\Models\User;
use MotorORM\Pagination;

/** @var User $user */
/** @var Pagination|Story[] $stories */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Статьи <?= $user->getName() ?> (Стр. <?= $stories->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('description') ?>Статьи <?= $user->getName() ?> (Стр. <?= $stories->currentPage() ?>)<?php $this->stop() ?>

<?php $this->start('header') ?>
    <h2 class="page-title">Статьи <?= $user->getName() ?></h2>
<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item"><a href="/users/<?= $user->login ?>"><?= $user->getName() ?></a></li>
        <li class="breadcrumb-item active">Статьи</li>
    </ol>
</nav>
<?php $this->stop() ?>

<?= $this->fetch('stories/_list', compact('stories') + [
    'emptyTitle'    => 'Статей нет',
    'emptySubtitle' => 'Этот пользователь пока ничего не написал',
]) ?>
