<?php

use League\Plates\Template\Template;

/** @var Template $template */
/** @var array $settings */
?>

<?php $template->start('title') ?>Основные настройки<?php $template->stop() ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

    <div class="form-check mb-3">
        <input type="hidden" value="0" name="settings[story][allow_register]">
        <input type="checkbox" class="form-check-input" value="1" name="settings[main][allow_register]" id="allow_register"<?= old('settings.main.allow_register', $settings['main.allow_register']) ? ' checked' : '' ?>>
        <label class="form-check-label" for="allow_register">Разрешить регистрацию</label>
    </div>

    <div class="mb-3">
        <label for="title" class="form-label">Заголовок сайта</label>
        <input type="text" class="form-control<?= hasError('title') ?>" id="title" name="settings[main][title]" value="<?= old('settings.main.title', $settings['main.title']) ?>" required>
        <div class="invalid-feedback"><?= getError('title') ?></div>
    </div>

    <div class="mb-3">
        <label for="guest_name" class="form-label">Имя гостя:</label>
        <input type="text" class="form-control<?= hasError('guest_name') ?>" id="guest_name" name="settings[main][guest_name]" value="<?= old('settings.main.guest_name', $settings['main.guest_name']) ?>" required>
        <div class="invalid-feedback"><?= getError('guest_name') ?></div>
    </div>

    <div class="mb-3">
        <label for="delete_name" class="form-label">Имя удаленного пользователя:</label>
        <input type="text" class="form-control<?= hasError('delete_name') ?>" id="delete_name" name="settings[main][delete_name]" value="<?= old('settings.main.delete_name', $settings['main.delete_name']) ?>" required>
        <div class="invalid-feedback"><?= getError('delete_name') ?></div>
    </div>

    <button class="btn btn-primary">Сохранить</button>
</form>
