<?php

use App\Models\Story;
use MotorORM\CollectionPaginate;

/** @var CollectionPaginate|Story[] $posts */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Статьи (Стр. <?= $posts->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('description') ?>Статьи (Стр. <?= $posts->currentPage() ?>)<?php $this->stop() ?>

<?php $this->start('header') ?>
    <?php if (isUser()): ?>
        <div class="float-end"><a class="btn btn-success" href="/create">Добавить</a></div>
    <?php endif; ?>

    <h1><?= setting('main.title') ?></h1>
<?php $this->stop() ?>

<?php if ($posts->isNotEmpty()): ?>
    <?php foreach ($posts as $post): ?>
        <article class="section shadow p-3 mb-3">
            <div class="float-end js-rating">
                <?php if (getUser() && getUser('id') !== $post->user_id): ?>
                    <a href="#" class="post-rating-up<?= $post->poll->vote === '+' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $post->id ?>" data-vote="+" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-up"></i></a>
                <?php endif; ?>

                <b><?= $post->getRating() ?></b>

                <?php if (getUser() && getUser('id') !== $post->user_id): ?>
                    <a href="#" class="post-rating-down<?= $post->poll->vote === '-' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $post->id ?>" data-vote="-" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-down"></i></a>
                <?php endif; ?>
            </div>

            <h5><a href="/<?= $post->getLink() ?>"><?= $this->e($post->title) ?></a></h5>

            <div class="message">
                <?= $post->shortText(setting('story.short_words')) ?>
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

                <?php if (isAdmin()): ?>
                    <div class="float-end ms-3">
                        <!--<i class="bi bi-three-dots-vertical"></i>-->

                        <a href="/<?= $post->id ?>/edit"><i class="bi bi-pencil"></i></a>
                        <a href="/<?= $post->id ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete"><i class="bi bi-x-lg"></i></a>
                    </div>
                <?php endif; ?>
            </div>

            <small class="fw-bold">
                <i class="bi bi-chat"></i>
                <a href="/<?= $post->getLink() ?>#comments">Комментарии: <?= $post->comments()->count() ?></a>
            </small>
        </article>
    <?php endforeach; ?>

    <?= $posts->links() ?>
<?php else: ?>
    <div class="alert alert-danger">Статей еще нет!</div>
<?php endif; ?>
