<?php

use League\Plates\Template\Template;

/** @var Template $template */
/** @var array $settings */
?>

<?php $template->start('title') ?>Комментарии<?php $template->stop() ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

    <div class="mb-3">
        <label for="text_min_length" class="form-label">Минимальная длина комментария</label>
        <input type="text" class="form-control<?= hasError('text_min_length') ?>" id="text_min_length" name="settings[comment][text_min_length]" value="<?= old('settings.comment.text_min_length',  $settings['comment.text_min_length']) ?>" required>
        <div class="invalid-feedback"><?= getError('text_min_length') ?></div>
    </div>

    <div class="mb-3">
        <label for="text_max_length" class="form-label">Максимальная длина комментария</label>
        <input type="text" class="form-control<?= hasError('text_max_length') ?>" id="text_max_length" name="settings[comment][text_max_length]" value="<?= old('settings.comment.text_max_length',  $settings['comment.text_max_length']) ?>" required>
        <div class="invalid-feedback"><?= getError('text_max_length') ?></div>
    </div>

    <button class="btn btn-primary">Сохранить</button>
</form>
