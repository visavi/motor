<?php

use League\Plates\Template\Template;

/** @var Template $template */
/** @var array $settings */
?>

<?php $template->start('title') ?>Стикеры<?php $template->stop() ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

    <div class="mb-3">
        <label for="size_max" class="form-label">Максимальный вес (Kb)</label>
        <input type="number" class="form-control<?= hasError('size_max') ?>" id="size_max" name="settings[sticker][size_max]" value="<?= old('settings.sticker.size_max',  round($settings['sticker.size_max'] / 1024)) ?>" required>
        <div class="invalid-feedback"><?= getError('size_max') ?></div>

        <input type="hidden" value="1024" name="modifier[sticker][size_max]">
    </div>

    <div class="mb-3">
        <label for="weight_max" class="form-label">Максимальный размер (px)</label>
        <input type="number" class="form-control<?= hasError('weight_max') ?>" id="weight_max" name="settings[sticker][weight_max]" value="<?= old('settings.sticker.weight_max',  $settings['sticker.weight_max']) ?>">
        <div class="invalid-feedback"><?= getError('weight_max') ?></div>
    </div>

    <div class="mb-3">
        <label for="weight_min" class="form-label">Минимальный размер (px)</label>
        <input type="number" class="form-control<?= hasError('weight_min') ?>" id="weight_min" name="settings[sticker][weight_min]" value="<?= old('settings.sticker.weight_min',  $settings['sticker.weight_min']) ?>">
        <div class="invalid-feedback"><?= getError('weight_min') ?></div>
    </div>


    <div class="mb-3">
        <label for="per_page" class="form-label">Количество на стр.</label>
        <input type="number" class="form-control<?= hasError('per_page') ?>" id="per_page" name="settings[sticker][per_page]" value="<?= old('settings.sticker.per_page',  $settings['sticker.per_page']) ?>" required>
        <div class="invalid-feedback"><?= getError('per_page') ?></div>
    </div>

    <button class="btn btn-primary">Сохранить</button>
</form>
