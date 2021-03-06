<?php
use App\Models\User;

/** @var User $user */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?><?= $user->login ?><?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active"><?= $user->login ?></li>
        </ol>
    </nav>
<?php $this->stop() ?>

<div class="section p-3 shadow">
    <div class="row">
        <div class="col-md-6">
            <div>ID: <?= $user->id ?></div>
            <div>Логин: <?= $user->login ?></div>
            <div>Роль: <?= setting('roles.' . $user->role) ?? 'Пользователь' ?></div>
            <div>Имя: <?= $this->e($user->name) ?></div>
            <div>Дата регистрации: <?= date('d.m.Y', $user->created_at) ?></div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <?php if ($user->picture): ?>
                    <img src="<?= $user->picture ?>" alt="Фото <?= $user->login ?>" class="img-fluid">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (getUser('id') === $user->id): ?>
        <div class="mt-3">
            <i class="bi bi-person"></i> <a href="/profile">Профиль</a>
        </div>
    <?php endif; ?>
</div>
