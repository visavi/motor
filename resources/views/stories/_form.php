<?php

use App\Models\File;
use App\Models\Story;
use MotorORM\Collection;

/** @var Story|null $post */
/** @var Collection<File> $files */
?>
<div class="p-3 shadow cut">
    <form method="post" action="/<?=  $post->id ?? '' ?>">
        <input type="hidden" name="_METHOD" value="<?= $post ? 'PUT' : 'POST' ?>">
        <input type="hidden" name="csrf" value="<?= session('csrf') ?>">
        <div class="mb-3">
            <label for="title" class="form-label">Заголовок</label>
            <input type="text" class="form-control<?= hasError('title') ?>" id="title" name="title" value="<?= old('title', $post->title ?? null) ?>" required>
            <div class="invalid-feedback"><?= getError('title') ?></div>
        </div>

        <div class="mb-3">
            <label for="text" class="form-label">Текст</label>
            <textarea class="form-control markItUp<?= hasError('text') ?>" id="text" rows="5" name="text" maxlength="<?= setting('story.text_max_length') ?>" required><?= old('text', $post->text ?? null) ?></textarea>
            <span class="js-textarea-counter"></span>
            <div class="invalid-feedback"><?= getError('text') ?></div>
        </div>

        <?= $this->fetch('app/_upload', compact('post','files')) ?>

        <button type="submit" class="btn btn-primary"><?= $post ? 'Изменить' : 'Отправить' ?></button>
    </form>
</div>
