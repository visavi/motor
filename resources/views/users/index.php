<?php

use App\Models\User;
use MotorORM\Pagination;

/** @var Pagination|User[] $users */
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
        <div class="card card-body mb-3">
            <div class="float-end">
                <?php if ($user->isBanned()): ?>
                    <span class="badge text-bg-danger"><?= setting('roles.banned') ?></span>
                <?php endif; ?>

                <?php if ($user->isPended()): ?>
                    <span class="badge text-bg-warning"><?= setting('roles.pended') ?></span>
                <?php endif; ?>
            </div>

            <div class="mb-3"><?= $user->getAvatar('lg') ?></div>
            <a class="fw-bold" href="/users/<?= $user->login ?>"><?= $user->getName() ?></a><br>
            Роль: <?= $user->getRole() ?><br>
            Регистрация: <?= date('d.m.Y', $user->created_at) ?>

            <?php if (isAdmin()): ?>
                <div class="float-end ms-3">
                    <a href="<?= route('user-edit', ['login' => $user->login]) ?>" title="Редактировать" data-bs-toggle="tooltip"><i class="bi bi-pencil"></i></a>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?= pagination($users) ?>
<?php else: ?>
    <?= $this->fetch('app/_empty', [
        'title'    => 'Пользователей ещё нет',
        'subtitle' => 'Как только кто-то зарегистрируется, он окажется здесь',
        'icon'     => 'bi-people',
    ]) ?>
<?php endif; ?>
