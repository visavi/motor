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
            <input type="text" class="form-control<?= hasError('tags') ?>" id="tags" name="tags" value="<?= old('tags', $story->tags ?? null) ?>" required>
            <div class="form-text">
                Через запятую
            </div>
            <div class="invalid-feedback"><?= getError('tags') ?></div>
        </div>

        <button type="submit" class="btn btn-primary"><?= $story ? 'Изменить' : 'Отправить' ?></button>
    </form>
</div>
