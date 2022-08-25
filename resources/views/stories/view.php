<?php

use App\Models\Story;

/** @var Story $story */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?><?= $this->e($story->title) ?><?php $this->stop() ?>
<?php $this->start('description') ?><?= $this->e($story->title) ?><?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item active"><?= $this->e($story->title) ?></li>
    </ol>
</nav>
<?php $this->stop() ?>

<div class="section shadow p-3 mb-3">
    <div class="float-end js-rating">
        <?php if (getUser() && getUser('id') !== $story->user_id): ?>
            <a href="#" class="post-rating-up<?= $story->poll->vote === '+' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $story->id ?>" data-vote="+" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-up"></i></a>
        <?php endif; ?>

        <b><?= $story->getRating() ?></b>

        <?php if (getUser() && getUser('id') !== $story->user_id): ?>
            <a href="#" class="post-rating-down<?= $story->poll->vote === '-' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $story->id ?>" data-vote="-" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-down"></i></a>
        <?php endif; ?>
    </div>

    <div class="message">
        <?= bbCode($story->text) ?>
    </div>

    <div class="section-author">
        <span class="avatar-micro">
            <?= $story->user->getAvatar() ?>
        </span>
        <span><?= $story->user->getProfile() ?></span>

        <small class="text-muted fst-italic ms-1"><?= date('d.m.Y H:i', $story->created_at) ?></small>
    </div>

    <div class="my-3 fst-italic">
        <i class="bi bi-tags"></i> <?= $story->getTags() ?>
    </div>

    <small class="fw-bold">
        <i class="bi bi-eye"></i> Просмотры: <?= $story->reads ?>
    </small>

    <?php if (isAdmin()): ?>
        <div class="float-end">
            <a href="/<?= $story->id ?>/edit"><i class="bi bi-pencil"></i></a>
            <a href="/<?= $story->id ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete"><i class="bi bi-x-lg"></i></a>
        </div>
    <?php endif; ?>
</div>

<div class="section shadow p-3 mb-3" id="comments">
    <h5>Комментарии</h5>

    <?php if ($story->comments->isNotEmpty()): ?>
        <?php foreach ($story->comments as $comment): ?>
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
                            <a href="/<?= $story->id ?>/comments/<?= $comment->id ?>/edit"><i class="bi bi-pencil"></i></a>
                            <a href="/<?= $story->id ?>/comments/<?= $comment->id ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete"><i class="bi bi-x-lg"></i></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill text-danger"></i>
            Комментариев еще нет!
        </div>
    <?php endif; ?>

    <?php if (isUser()): ?>
        <?= $this->fetch('comments/_form', compact('story')) ?>
    <?php else: ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill text-danger"></i>
            Для выполнения действия необходимо авторизоваться!
        </div>
    <?php endif; ?>
</div>
