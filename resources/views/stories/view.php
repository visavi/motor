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

<div class="shadow p-3 mb-3">
    <div class="float-end js-rating">
        <?php if (getUser() && getUser('id') !== $post->user_id): ?>
            <a href="#" class="post-rating-up" onclick="return changeRating(this);" data-id="<?= $post->id ?>" data-vote="+" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-up"></i></a>
        <?php endif; ?>

        <b><?= $post->getRating() ?></b>

        <?php if (getUser() && getUser('id') !== $post->user_id): ?>
            <a href="#" class="post-rating-down" onclick="return changeRating(this);" data-id="<?= $post->id ?>" data-vote="-" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-down"></i></a>
        <?php endif; ?>
    </div>

    <div class="message">
        <?= bbCode($post->text) ?>
    </div>

    <div class="section-author">
        <?php if ($post->user()): ?>
            <span class="avatar-micro">
                <?= $post->user()->getAvatar() ?>
            </span>
            <span><a href="/users/<?= $post->user()->login ?>"><?= $post->user()->getName() ?></a></span>
        <?php else: ?>
            <span class="avatar-micro">
                <img class="avatar-default rounded-circle" src="/assets/images/avatar_default.png" alt="Аватар">
            </span>
            <span><?= setting('main.delete_name') ?></span>
        <?php endif; ?>

        <small class="text-muted fst-italic ms-1"><?= date('d.m.Y H:i', $post->created_at) ?></small>

        <?php if (isAdmin()): ?>
            <div class="float-end">
                <a href="/<?= $post->id ?>/edit"><i class="bi bi-pencil"></i></a>
                <a href="/<?= $post->id ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete"><i class="bi bi-x-lg"></i></a>
            </div>
        <?php endif; ?>
    </div>
</div>
