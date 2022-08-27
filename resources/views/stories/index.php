<?php

use App\Models\Story;
use MotorORM\CollectionPaginate;

/** @var CollectionPaginate|Story[] $stories */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Статьи (Стр. <?= $stories->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('description') ?>Статьи (Стр. <?= $stories->currentPage() ?>)<?php $this->stop() ?>

<?php $this->start('header') ?>
    <?php if (isUser()): ?>
        <div class="float-end"><a class="btn btn-success" href="/create">Добавить</a></div>
    <?php endif; ?>

    <h1><?= setting('main.title') ?></h1>
<?php $this->stop() ?>

<?php if ($stories->isNotEmpty()): ?>
    <?php foreach ($stories as $story): ?>
        <article class="section shadow p-3 mb-3">
            <div class="float-end js-rating">
                <?php if (getUser() && getUser('id') !== $story->user_id): ?>
                    <a href="#" class="post-rating-down<?= $story->poll->vote === '-' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $story->id ?>" data-vote="-" data-type="story" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-down"></i></a>
                <?php endif; ?>

                <b><?= $story->getRating() ?></b>

                <?php if (getUser() && getUser('id') !== $story->user_id): ?>
                    <a href="#" class="post-rating-up<?= $story->poll->vote === '+' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $story->id ?>" data-vote="+" data-type="story" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-up"></i></a>
                <?php endif; ?>
            </div>

            <h5>
                <a href="<?= $story->getLink() ?>"><?= $this->e($story->title) ?></a>
                <?php if ($story->locked): ?>
                    <small><i class="bi bi-pin-angle"></i></small>
                <?php endif; ?>
            </h5>

            <div class="post-message">
                <?= $story->shortText(setting('story.short_words')) ?>
            </div>

            <div class="post-author mt-3">
                <span class="avatar-micro">
                    <?= $story->user->getAvatar() ?>
                </span>
                <span><?= $story->user->getProfile() ?></span>

                <small class="text-muted fst-italic ms-1"><?= date('d.m.Y H:i', $story->created_at) ?></small>
            </div>

            <div class="my-3 fst-italic">
                <i class="bi bi-tags"></i> <?= $story->getTags() ?>
            </div>

            <div class="border rounded p-2">
                <div class="d-inline fw-bold fs-6 me-3" title="Комментарии" data-bs-toggle="tooltip">
                    <a href="<?= $story->getLink() ?>#comments"><i class="bi bi-chat"></i> <?= $story->comments()->count() ?></a>
                </div>

                <div class="d-inline fw-bold fs-6 me-3" title="Просмотры" data-bs-toggle="tooltip">
                    <i class="bi bi-eye"></i> <?= $story->reads ?>
                </div>

                <?php if ($story->user_id === getUser('id') || isAdmin()): ?>
                    <div class="float-end ms-3">
                        <!-- <i class="bi bi-three-dots-vertical"></i> -->

                        <a href="/<?= $story->id ?>/edit"><i class="bi bi-pencil text-muted"></i></a>
                        <a href="/<?= $story->id ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete"><i class="bi bi-x-lg text-muted"></i></a>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>

    <?= $stories->links() ?>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Статей еще нет!
    </div>
<?php endif; ?>
