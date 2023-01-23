<?php

use App\Models\User;
use MotorORM\CollectionPaginate;

/** @var CollectionPaginate|User[] $users */
?>

<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Пользователи<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Пользователи</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($users->isNotEmpty()): ?>
    <?php foreach ($users as $user): ?>
        <div class="section shadow border p-3 mb-3">
            <span class="avatar-default mb-3">
                <?= $user->getAvatar() ?>
            </span>
            <a class="fw-bold" href="/users/<?= $user->login ?>"><?= $user->getName() ?></a><br>
            Роль: <?= $user->getRole() ?><br>
            Регистрация: <?= date('d.m.Y', $user->created_at) ?>
        </div>
    <?php endforeach; ?>

    <?= $users->links() ?>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Пользователей еще нет!
    </div>
<?php endif; ?>
