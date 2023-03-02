<?php

use App\Models\Comment;
use App\Models\Story;

/** @var Story $story */
/** @var Comment|null $comment */

$comment ??= null;
?>

<div class="section shadow border p-3 post-form">
    <form method="post" action="/stories/<?= $story->id ?>/comments<?= $comment ? '/' . $comment->id : '' ?>">
        <input type="hidden" name="_METHOD" value="<?= $comment ? 'PUT' : 'POST' ?>">
        <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

        <div class="mb-3">
            <label for="text" class="form-label">Сообщение</label>
            <textarea class="form-control markItUp<?= hasError('text') ?>" id="text" rows="5" name="text" maxlength="<?= setting('comment.text_max_length') ?>" required><?= old('text', $comment->text ?? null) ?></textarea>
            <span class="js-textarea-counter"></span>
            <div class="invalid-feedback"><?= getError('text') ?></div>
        </div>

        <button type="submit" class="btn btn-primary"><?= $comment ? 'Изменить' : 'Отправить' ?></button>
    </form>
</div>
