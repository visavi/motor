<?php

use League\Plates\Template\Template;

/** @var Template $template */
/** @var array $settings */
?>

<?php $template->start('title') ?>Почта<?php $template->stop() ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

    <div class="mb-3">
        <label for="dsn" class="form-label">DSN:</label>
        <input type="text" class="form-control<?= hasError('dsn') ?>" id="dsn" name="settings[mailer][dsn]" value="<?= old('settings.mailer.dsn', $settings['mailer.dsn']) ?>" required>
        <div class="invalid-feedback"><?= getError('dsn') ?></div>
    </div>

    <div class="mb-3">
        <label for="from_email" class="form-label">Email отправителя:</label>
        <input type="text" class="form-control<?= hasError('from_email') ?>" id="from_email" name="settings[mailer][from_email]" value="<?= old('settings.mailer.from_email', $settings['mailer.from_email']) ?>" required>
        <div class="invalid-feedback"><?= getError('from_email') ?></div>
    </div>

    <div class="mb-3">
        <label for="from_name" class="form-label">Имя отправителя:</label>
        <input type="text" class="form-control<?= hasError('from_name') ?>" id="from_name" name="settings[mailer][from_name]" value="<?= old('settings.mailer.from_name', $settings['mailer.from_name']) ?>" required>
        <div class="invalid-feedback"><?= getError('from_name') ?></div>
    </div>

    <button class="btn btn-primary">Сохранить</button>
</form>
