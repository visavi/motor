<?php
use App\Models\User;

/** @var User $user */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?><?= $user->getName() ?><?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="/users">Пользователи</a></li>
            <li class="breadcrumb-item active"><?= $user->getName() ?></li>
        </ol>
    </nav>
<?php $this->stop() ?>

<div class="section p-3 shadow">
    <div class="row">
        <div class="col-md-6">
            <div>ID: <?= $user->id ?></div>
            <div>Логин: <?= $user->login ?></div>
            <div>Роль: <?= $user->getRole() ?></div>
            <div>Имя: <?= escape($user->name) ?></div>
            <div>Регистрация: <?= date('d.m.Y', $user->created_at) ?></div>

            <div class="mt-3"><i class="bi bi-card-heading"></i> <a href="/users/<?= $user->login ?>/stories">Все статьи <?= $user->getName() ?></a></div>
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
