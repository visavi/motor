<?php

use League\Plates\Template\Template;

/** @var Template $template */
/** @var array $settings */
?>

<?php $template->start('title') ?>Статьи<?php $template->stop() ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

    <div class="form-check mb-3">
        <input type="hidden" value="0" name="settings[story][active]">
        <input type="checkbox" class="form-check-input" value="1" name="settings[story][active]" id="active"<?= old('settings.story.active', $settings['story.active']) ? ' checked' : '' ?>>
        <label class="form-check-label" for="active">Публиковать посты без модерации</label>
    </div>

    <div class="form-check mb-3">
        <input type="hidden" value="0" name="settings[story][allow_posting]">
        <input type="checkbox" class="form-check-input" value="1" name="settings[story][allow_posting]" id="allow_posting"<?= old('settings.story.allow_posting', $settings['story.allow_posting']) ? ' checked' : '' ?>>
        <label class="form-check-label" for="allow_posting">Разрешать пользователям публиковать статьи</label>
    </div>

    <div class="mb-3">
        <label for="per_page" class="form-label">Количество статей на страницу</label>
        <input type="text" class="form-control<?= hasError('per_page') ?>" id="per_page" name="settings[story][per_page]" value="<?= old('settings.story.per_page',  $settings['story.per_page']) ?>" required>
        <div class="invalid-feedback"><?= getError('per_page') ?></div>
    </div>

    <div class="mb-3">
        <label for="title_min_length" class="form-label">Минимальная длина заголовка</label>
        <input type="text" class="form-control<?= hasError('title_min_length') ?>" id="title_min_length" name="settings[story][title_min_length]" value="<?= old('settings.story.title_min_length',  $settings['story.title_min_length']) ?>" required>
        <div class="invalid-feedback"><?= getError('title_min_length') ?></div>
    </div>

    <div class="mb-3">
        <label for="title_max_length" class="form-label">Максимальная длина заголовка</label>
        <input type="text" class="form-control<?= hasError('title_max_length') ?>" id="title_max_length" name="settings[story][title_max_length]" value="<?= old('settings.story.title_max_length',  $settings['story.title_max_length']) ?>" required>
        <div class="invalid-feedback"><?= getError('title_max_length') ?></div>
    </div>

    <div class="mb-3">
        <label for="text_min_length" class="form-label">Минимальная длина статьи</label>
        <input type="text" class="form-control<?= hasError('text_min_length') ?>" id="text_min_length" name="settings[story][text_min_length]" value="<?= old('settings.story.text_min_length',  $settings['story.text_min_length']) ?>" required>
        <div class="invalid-feedback"><?= getError('text_min_length') ?></div>
    </div>

    <div class="mb-3">
        <label for="text_max_length" class="form-label">Максимальная длина статьи</label>
        <input type="text" class="form-control<?= hasError('text_max_length') ?>" id="text_max_length" name="settings[story][text_max_length]" value="<?= old('settings.story.text_max_length',  $settings['story.text_max_length']) ?>" required>
        <div class="invalid-feedback"><?= getError('text_max_length') ?></div>
    </div>

    <div class="mb-3">
        <label for="short_words" class="form-label">Количество слов в сокращенной статье</label>
        <input type="text" class="form-control<?= hasError('short_words') ?>" id="short_words" name="settings[story][short_words]" value="<?= old('settings.story.short_words',  $settings['story.short_words']) ?>" required>
        <div class="invalid-feedback"><?= getError('short_words') ?></div>
    </div>

    <div class="mb-3">
        <label for="tags_max" class="form-label">Максимальное количество тегов</label>
        <input type="text" class="form-control<?= hasError('tags_max') ?>" id="tags_max" name="settings[story][tags_max]" value="<?= old('settings.story.tags_max',  $settings['story.tags_max']) ?>" required>
        <div class="invalid-feedback"><?= getError('tags_max') ?></div>
    </div>

    <div class="mb-3">
        <label for="tags_min_length" class="form-label">Минимальное количество символов в теге</label>
        <input type="text" class="form-control<?= hasError('tags_min_length') ?>" id="tags_min_length" name="settings[story][tags_min_length]" value="<?= old('settings.story.tags_min_length',  $settings['story.tags_min_length']) ?>" required>
        <div class="invalid-feedback"><?= getError('tags_min_length') ?></div>
    </div>

    <div class="mb-3">
        <label for="tags_max_length" class="form-label">Максимальное количество символов в теге</label>
        <input type="text" class="form-control<?= hasError('tags_max_length') ?>" id="tags_max_length" name="settings[story][tags_max_length]" value="<?= old('settings.story.tags_max_length',  $settings['story.tags_max_length']) ?>" required>
        <div class="invalid-feedback"><?= getError('tags_max_length') ?></div>
    </div>

    <button class="btn btn-primary">Сохранить</button>
</form>
