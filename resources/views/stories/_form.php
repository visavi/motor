<?php

use App\Models\File;
use App\Models\Story;
use MotorORM\Collection;

/** @var Story|null $story */
/** @var Collection<File> $files */

$story ??= null;
?>
<div class="section p-3 shadow cut">
    <form method="post" action="/<?=  $story->id ?? '' ?>">
        <input type="hidden" name="_METHOD" value="<?= $story ? 'PUT' : 'POST' ?>">
        <input type="hidden" name="csrf" value="<?= session('csrf') ?>">
        <div class="mb-3">
            <label for="title" class="form-label">Заголовок</label>
            <input type="text" class="form-control<?= hasError('title') ?>" id="title" name="title" value="<?= old('title', $story->title ?? null) ?>" required>
            <div class="invalid-feedback"><?= getError('title') ?></div>
        </div>

        <div class="mb-3">
            <label for="text" class="form-label">Текст</label>
            <textarea class="form-control markItUp<?= hasError('text') ?>" id="text" rows="5" name="text" maxlength="<?= setting('story.text_max_length') ?>" required><?= old('text', $story->text ?? null) ?></textarea>
            <span class="js-textarea-counter"></span>
            <div class="invalid-feedback"><?= getError('text') ?></div>
        </div>

        <?= $this->fetch('app/_upload', compact('story', 'files')) ?>

        <div class="mb-3">
            <label for="tags" class="form-label">Теги</label>

            <?php $tags = old('tags', $story ? $story->tags->pluck('tag') : []); ?>
            <select class="form-select input-tag<?= hasError('tags') ?>" id="tags" name="tags[]" multiple>
                <option disabled value="">Выберите тег...</option>
                <?php foreach ($tags as $key => $tag): ?>
                <option value="<?= $tag ?>" selected="selected"><?= $tag ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback"><?= getError('tags') ?></div>
        </div>

        <?php if (isAdmin()): ?>
            <div class="form-check">
                <input type="hidden" value="0" name="locked">
                <input type="checkbox" class="form-check-input" value="1" name="locked" id="locked"<?= old('locked', $story->locked ?? null) ? ' checked' : '' ?>>
                <label for="locked" class="form-check-label">Закрепить статью</label>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary"><?= $story ? 'Изменить' : 'Отправить' ?></button>
    </form>
</div>
