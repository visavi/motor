<?php

use League\Plates\Template\Template;

/** @var Template $template */
/** @var array $settings */
?>

<?php $template->start('title') ?>Файлы<?php $template->stop() ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

    <div class="mb-3">
        <label for="size_max" class="form-label">Максимальный вес файла (Mb)</label>
        <input type="number" class="form-control<?= hasError('size_max') ?>" id="size_max" name="settings[file][size_max]" value="<?= old('settings.file.size_max',  round($settings['file.size_max'] / 1048576)) ?>" required>
        <div class="invalid-feedback"><?= getError('size_max') ?></div>

        <input type="hidden" value="1048576" name="modifier[file][size_max]">
    </div>

    <div class="mb-3">
        <label for="total_max" class="form-label">Максимальное количество загружаемых файлов</label>
        <input type="number" class="form-control<?= hasError('total_max') ?>" id="total_max" name="settings[file][total_max]" value="<?= old('settings.file.total_max',  $settings['file.total_max']) ?>" required>
        <div class="invalid-feedback"><?= getError('total_max') ?></div>
    </div>

    <div class="mb-3">
        <label for="extensions" class="form-label">Допустимые расширения</label>
        <input type="text" class="form-control<?= hasError('extensions') ?>" id="extensions" name="settings[file][extensions]" value="<?= old('settings.file.extensions',  $settings['file.extensions']) ?>" required>
        <div class="invalid-feedback"><?= getError('extensions') ?></div>
    </div>

    <button class="btn btn-primary">Сохранить</button>
</form>
