<?php

use League\Plates\Template\Template;

/** @var Template $template */
/** @var array $settings */
?>

<?php $template->start('title') ?>Сайт<?php $template->stop() ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

    <div class="mb-3">
        <label for="name" class="form-label">Название сайта:</label>
        <input type="text" class="form-control<?= hasError('name') ?>" id="name" name="settings[app][name]" value="<?= old('settings.app.name', $settings['app.name']) ?>" required>
        <div class="invalid-feedback"><?= getError('name') ?></div>
    </div>

    <div class="mb-3">
        <label for="url" class="form-label">URL сайта:</label>
        <input type="text" class="form-control<?= hasError('url') ?>" id="url" name="settings[app][url]" value="<?= old('settings.app.url', $settings['app.url']) ?>" required>
        <div class="invalid-feedback"><?= getError('url') ?></div>
    </div>

    <button class="btn btn-primary">Сохранить</button>
</form>
