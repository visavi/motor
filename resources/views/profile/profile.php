<?php
use App\Models\User;

/** @var User $user */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Профиль <?= $user->login ?><?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Профиль <?= $user->login ?></li>
        </ol>
    </nav>
<?php $this->stop() ?>

<div class="p-3 shadow">
    <form method="post" action="/profile">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control<?= hasError('email') ?>" id="email" name="email" value="<?= old('email', $user->email) ?>" required>
            <div class="invalid-feedback"><?= getError('email') ?></div>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Имя</label>
            <input type="text" class="form-control<?= hasError('name') ?>" id="name" name="name" value="<?= old('name', $user->name) ?>" required>
            <div class="invalid-feedback"><?= getError('name') ?></div>
        </div>

        <button type="submit" class="btn btn-primary">Изменить</button>
    </form>
</div>
