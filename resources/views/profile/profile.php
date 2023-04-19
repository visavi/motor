<?php
use App\Models\User;

/** @var User $user */

$isManagement ??= false;
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Профиль <?= $user->login ?><?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('users') ?>">Пользователи</a></li>
            <li class="breadcrumb-item"><a href="/users/<?= $user->login ?>"><?= $user->login ?></a></li>
            <li class="breadcrumb-item active">Профиль <?= $user->login ?></li>
        </ol>
    </nav>
<?php $this->stop() ?>

<div class="section shadow border p-3">
    <form method="post" action="<?= route($isManagement ? 'user-store' : 'profile-store', ['login' => $user->login]) ?>" enctype="multipart/form-data">
        <input type="hidden" name="_METHOD" value="PUT">
        <input type="hidden" name="csrf" value="<?= session('csrf') ?>">
        <div class="row">
            <div class="col-md-6">
                <?php if ($isManagement): ?>
                    <?php $inputRole = old('role', $user->role) ?>
                    <div class="mb-3">
                        <label for="role" class="form-label">Роль:</label>
                        <select class="form-select<?= hasError('role') ?>" id="role" name="role">
                            <?php foreach (setting('roles') as $key => $role): ?>
                            <?php $selected = ($key === $inputRole) ? ' selected' : ''; ?>
                            <option value="<?= $key ?>"<?= $selected ?>><?= $role ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= getError('role') ?></div>
                    </div>
                <?php endif; ?>

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
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <?php if ($user->picture): ?>
                        <img src="<?= $user->picture ?>" alt="Фото <?= $user->login ?>" class="img-fluid">

                        <?php if (! $isManagement): ?>
                            <div class="float-end mt-3">
                                <a href="<?= route('profile-photo-destroy') ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete">Удалить фото</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="/assets/images/photo.png" alt="Нет фото" class="img-fluid">
                    <?php endif; ?>
                </div>

                <?php if (! $isManagement): ?>
                    <div class="mb-3">
                        <label for="picture" class="btn btn-sm btn-secondary form-label<?= hasError('picture') ?>">
                            <input id="picture" type="file" name="picture" onchange="$('#upload-file-info').html(this.files[0].name);" hidden>
                            Прикрепить фото&hellip;
                        </label>
                        <div class="invalid-feedback"><?= getError('picture') ?></div>
                        <span class="badge bg-info" id="upload-file-info"></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Изменить</button>
    </form>

    <?php if ($isManagement && isAdmin(User::ADMIN)): ?>
        <div class="mt-3">
            <i class="bi bi-x-lg"></i> <a href="<?= route('user-destroy', ['login' => $user->login]) ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete">Удалить пользователя</a>
        </div>
    <?php endif; ?>
</div>
