<?php

use League\Plates\Template\Template;

/** @var Template $template */
/** @var array $settings */
?>

<?php $template->start('title') ?>Изображения<?php $template->stop() ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

    <div class="mb-3">
        <label for="resize" class="form-label">Обрезать изображения (px)</label>
        <input type="number" class="form-control<?= hasError('resize') ?>" id="resize" name="settings[image][resize]" value="<?= old('settings.image.resize',  $settings['image.resize']) ?>" required>
        <div class="invalid-feedback"><?= getError('resize') ?></div>
    </div>

    <div class="mb-3">
        <label for="weight_max" class="form-label">Максимальный размер (px)</label>
        <input type="number" class="form-control<?= hasError('weight_max') ?>" id="weight_max" name="settings[image][weight_max]" value="<?= old('settings.image.weight_max',  $settings['image.weight_max']) ?>">
        <div class="invalid-feedback"><?= getError('weight_max') ?></div>

        <input type="hidden" value="1" name="optional[image][weight_max]">
    </div>

    <div class="mb-3">
        <label for="weight_min" class="form-label">Минимальный размер (px)</label>
        <input type="number" class="form-control<?= hasError('weight_min') ?>" id="weight_min" name="settings[image][weight_min]" value="<?= old('settings.image.weight_min',  $settings['image.weight_min']) ?>">
        <div class="invalid-feedback"><?= getError('weight_min') ?></div>

        <input type="hidden" value="1" name="optional[image][weight_min]">
    </div>

    <button class="btn btn-primary">Сохранить</button>
</form>
