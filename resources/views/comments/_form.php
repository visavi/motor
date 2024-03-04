<?php

use App\Models\Comment;
use App\Models\Story;

/** @var Story $story */
/** @var Comment|null $comment */

$comment ??= null;
?>

<div class="section shadow border p-3 post-form">
    <form method="post" action="<?= route($comment ? 'story-comment-update' : 'story-comment-store', ['id' => $story->id, 'cid' => $comment->id ?? null]) ?>">
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

<?php /* Скрытая форма быстрого ответа */ ?>
<div class="js-form" style="display: none;">
    <form method="post" action="<?= route('story-comment-store', ['id' => $story->id]) ?>">
        <input type="hidden" name="_METHOD" value="POST">
        <input type="hidden" name="csrf" value="<?= session('csrf') ?>">
        <input type="hidden" name="parent_id" value="0">

        <div class="mb-3">
            <textarea class="form-control" rows="5" name="text" maxlength="<?= setting('comment.text_max_length') ?>" required></textarea>
            <div class="invalid-feedback"></div>
        </div>

        <button type="submit" class="btn btn-primary" onclick="return sendComment(this);">Ответить</button>
    </form>
</div>

<?php /* Скрытый блок быстрого ответа */ ?>
<div class="js-post post mb-3 border-start" style="padding-left: 10px; display: none;">
    <div class="float-end text-end">
        <div class="js-rating">
            <b>0</b>
        </div>
    </div>
    <div class="post-author mb-1" data-login="@<?= getUser()->login ?>">
        <span class="avatar-micro">
            <?= getUser()->getAvatar() ?>
        </span>
        <span><?= getUser()->getProfile() ?></span>
    </div>
    <div class="post-message"></div>
    <small class="post-date text-body-secondary fst-italic"></small>
</div>
