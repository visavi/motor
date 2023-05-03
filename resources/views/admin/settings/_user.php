<?php

use League\Plates\Template\Template;

/** @var Template $template */
/** @var array $settings */
?>

<?php $template->start('title') ?>Комментарии<?php $template->stop() ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

    <div class="mb-3">
        <label for="per_page" class="form-label">Количество пользователей на стр.</label>
        <input type="text" class="form-control<?= hasError('per_page') ?>" id="per_page" name="settings[user][per_page]" value="<?= old('settings.user.per_page',  $settings['user.per_page']) ?>" required>
        <div class="invalid-feedback"><?= getError('per_page') ?></div>
    </div>

    <button class="btn btn-primary">Сохранить</button>
</form>
