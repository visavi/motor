<?php

use App\Models\Story;

/** @var Story $post */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?><?= $this->e($post->title) ?><?php $this->stop() ?>
<?php $this->start('description') ?><?= $this->e($post->title) ?><?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item active"><?= $this->e($post->title) ?></li>
    </ol>
</nav>
<?php $this->stop() ?>

<div class="section shadow p-3 mb-3">
    <div class="float-end js-rating">
        <?php if (getUser() && getUser('id') !== $post->user_id): ?>
            <a href="#" class="post-rating-up<?= $post->poll->vote === '+' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $post->id ?>" data-vote="+" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-up"></i></a>
        <?php endif; ?>

        <b><?= $post->getRating() ?></b>

        <?php if (getUser() && getUser('id') !== $post->user_id): ?>
            <a href="#" class="post-rating-down<?= $post->poll->vote === '-' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $post->id ?>" data-vote="-" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-down"></i></a>
        <?php endif; ?>
    </div>

    <div class="message">
        <?= bbCode($post->text) ?>
    </div>

    <div class="section-author">
        <span class="avatar-micro">
            <?= $post->user->getAvatar() ?>
        </span>
        <span><?= $post->user->getProfile() ?></span>

        <small class="text-muted fst-italic ms-1"><?= date('d.m.Y H:i', $post->created_at) ?></small>
    </div>

    <div class="my-3 fst-italic">
        <i class="bi bi-tags"></i> <?= $post->getTags() ?>
    </div>

    <small class="fw-bold">
        <i class="bi bi-eye"></i> Просмотры: <?= $post->reads ?>
    </small>

    <?php if (isAdmin()): ?>
        <div class="float-end">
            <a href="/<?= $post->id ?>/edit"><i class="bi bi-pencil"></i></a>
            <a href="/<?= $post->id ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete"><i class="bi bi-x-lg"></i></a>
        </div>
    <?php endif; ?>
</div>

<div class="section shadow p-3 mb-3" id="comments">
    <h5>Комментарии</h5>

    <?php if ($post->comments->isNotEmpty()): ?>
        <?php foreach ($post->comments as $comment): ?>
            <div class="mb-3">
                <div class="section-author">
                    <span class="avatar-micro">
                        <?= $comment->user->getAvatar() ?>
                    </span>
                    <span><?= $comment->user->getProfile() ?></span>
                </div>

                <div class="section-post">
                    <?= bbCode($comment->text) ?>
                    <small class="text-muted fst-italic ms-1"><?= date('d.m.Y H:i', $comment->created_at) ?></small>


                    <?php if (isAdmin()): ?>
                        <div class="float-end">
                            <a href="/<?= $post->id ?>/comments/<?= $comment->id ?>/edit"><i class="bi bi-pencil"></i></a>
                            <a href="/<?= $post->id ?>/comments/<?= $comment->id ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete"><i class="bi bi-x-lg"></i></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        Комментариев еще нет!
    <?php endif; ?>

    <div class="section shadow p-3 mt-3">
        <form method="post" action="/<?= $post->id ?>/comments">
            <input type="hidden" name="_METHOD" value="POST">
            <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

            <div class="mb-3">
                <label for="text" class="form-label">Текст</label>
                <textarea class="form-control markItUp<?= hasError('text') ?>" id="text" rows="5" name="text" maxlength="<?= setting('comment.text_max_length') ?>" required><?= old('text') ?></textarea>
                <span class="js-textarea-counter"></span>
                <div class="invalid-feedback"><?= getError('text') ?></div>
            </div>

            <button type="submit" class="btn btn-primary">Отправить</button>
        </form>
    </div>
</div>
