<?php

use League\Plates\Template\Template;

/** @var Template $template */
/** @var array $settings */
?>

<?php $template->start('title') ?>Captcha<?php $template->stop() ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

    <div class="mb-3">
        <label for="length" class="form-label">Количество символов</label>
        <input type="text" class="form-control<?= hasError('length') ?>" id="length" name="settings[captcha][length]" value="<?= old('settings.captcha.length',  $settings['captcha.length']) ?>" required>
        <div class="invalid-feedback"><?= getError('length') ?></div>
    </div>

    <div class="mb-3">
        <label for="symbols" class="form-label">Список допустимых символов</label>
        <input type="text" class="form-control<?= hasError('symbols') ?>" id="symbols" name="settings[captcha][symbols]" value="<?= old('settings.captcha.symbols',  $settings['captcha.symbols']) ?>" required>
        <div class="invalid-feedback"><?= getError('symbols') ?></div>
    </div>

    <button class="btn btn-primary">Сохранить</button>
</form>
