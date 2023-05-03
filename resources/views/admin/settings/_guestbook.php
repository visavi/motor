<?php

use League\Plates\Template\Template;

/** @var Template $template */
/** @var array $settings */
?>

<?php $template->start('title') ?>Гостевая<?php $template->stop() ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

    <div class="form-check mb-3">
        <input type="hidden" value="0" name="settings[guestbook][allow_guests]">
        <input type="checkbox" class="form-check-input" value="1" name="settings[guestbook][allow_guests]" id="allow_guests"<?= old('settings.guestbook.allow_guests', $settings['guestbook.allow_guests']) ? ' checked' : '' ?>>
        <label class="form-check-label" for="allow_guests">Разрешить гостям писать сообщения</label>
    </div>

    <div class="mb-3">
        <label for="per_page" class="form-label">Количество сообщений на стр.</label>
        <input type="text" class="form-control<?= hasError('per_page') ?>" id="per_page" name="settings[guestbook][per_page]" value="<?= old('settings.guestbook.per_page',  $settings['guestbook.per_page']) ?>" required>
        <div class="invalid-feedback"><?= getError('per_page') ?></div>
    </div>

    <div class="mb-3">
        <label for="text_min_length" class="form-label">Минимальное количество символов</label>
        <input type="text" class="form-control<?= hasError('text_min_length') ?>" id="text_min_length" name="settings[guestbook][text_min_length]" value="<?= old('settings.guestbook.text_min_length',  $settings['guestbook.text_min_length']) ?>" required>
        <div class="invalid-feedback"><?= getError('text_min_length') ?></div>
    </div>

    <div class="mb-3">
        <label for="text_max_length" class="form-label">Максимальное количество символов</label>
        <input type="text" class="form-control<?= hasError('text_max_length') ?>" id="text_max_length" name="settings[guestbook][text_max_length]" value="<?= old('settings.guestbook.text_max_length',  $settings['guestbook.text_max_length']) ?>" required>
        <div class="invalid-feedback"><?= getError('text_max_length') ?></div>
    </div>

    <div class="mb-3">
        <label for="name_min_length" class="form-label">Минимальная длина имени</label>
        <input type="text" class="form-control<?= hasError('name_min_length') ?>" id="name_min_length" name="settings[guestbook][name_min_length]" value="<?= old('settings.guestbook.name_min_length',  $settings['guestbook.name_min_length']) ?>" required>
        <div class="invalid-feedback"><?= getError('name_min_length') ?></div>
    </div>

    <div class="mb-3">
        <label for="name_max_length" class="form-label">Максимальная длина имени</label>
        <input type="text" class="form-control<?= hasError('name_max_length') ?>" id="name_max_length" name="settings[guestbook][name_max_length]" value="<?= old('settings.guestbook.name_max_length',  $settings['guestbook.name_max_length']) ?>" required>
        <div class="invalid-feedback"><?= getError('name_max_length') ?></div>
    </div>

    <button class="btn btn-primary">Сохранить</button>
</form>
