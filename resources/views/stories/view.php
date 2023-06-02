<?php

use App\Models\Comment;
use App\Models\Story;

/** @var Story $story */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?><?= escape($story->title) ?><?php $this->stop() ?>
<?php $this->start('description') ?><?= escape($story->title) ?><?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item"><a href="<?= route('stories') ?>">Статьи</a></li>
        <li class="breadcrumb-item active"><?= escape($story->title) ?></li>
    </ol>
</nav>
<?php $this->stop() ?>

<div class="section shadow border p-3 mb-3">
    <div class="float-end js-rating">
        <?php if ($story->active === false): ?>
            <span class="badge text-bg-danger">Не опубликовано</span>
        <?php endif; ?>

        <?php if ($story->created_at > time()): ?>
            <span class="badge text-bg-warning">Отложенная публикация</span>
        <?php endif; ?>

        <?php if (getUser('id') !== $story->user_id): ?>
            <a href="#" class="post-rating-down<?= $story->poll->vote === '-' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $story->id ?>" data-vote="-" data-type="story" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-down"></i></a>
        <?php endif; ?>

        <b><?= $story->getRating() ?></b>

        <?php if (getUser('id') !== $story->user_id): ?>
            <a href="#" class="post-rating-up<?= $story->poll->vote === '+' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $story->id ?>" data-vote="+" data-type="story" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-up"></i></a>
        <?php endif; ?>
    </div>

    <div class="post-message">
        <?= bbCode($story->text) ?>
    </div>

    <div class="post-author d-inline-block mt-3">
        <span class="avatar-micro">
            <?= $story->user->getAvatar() ?>
        </span>
        <span><?= $story->user->getProfile() ?></span>
    </div>

    <small class="post-date text-body-secondary fst-italic ms-1">
        <?= date('d.m.Y H:i', $story->created_at) ?>
    </small>

    <div class="my-3">
        <?php if ($story->files->isNotEmpty()): ?>
            <?php foreach ($story->files as $file): ?>
                <?php if (! $file->isImage()): ?>
                    <div class="media-file">
                    <?php if ($file->isAudio()): ?>
                        <audio src="<?= $file->path ?>" style="max-width:100%;" preload="metadata" controls></audio>
                    <?php elseif ($file->isVideo()): ?>
                        <video src="<?= $file->path ?>" style="max-width:100%;" preload="metadata" controls playsinline></video>
                    <?php else: ?>
                        <i class="bi bi-download"></i>
                        <a href="<?= $file->path ?>"><?= $file->name ?></a>
                        <?= formatSize($file->size) ?>
                    <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="my-3 fst-italic">
        <i class="bi bi-tags"></i> <?= $story->getTags() ?>
    </div>

    <div class="border rounded p-2">
        <div class="d-inline fw-bold fs-6 me-3" title="Избранное" data-bs-toggle="tooltip">
            <?php if (isUser()): ?>
                <a href="#" onclick="return addFavorite(this);" data-id="<?= $story->id ?>"  data-csrf="<?= session('csrf') ?>"><i class="bi <?= $story->favorite->id ? 'bi-heart-fill' : 'bi-heart' ?>"></i> <?= $story->favorites()->count() ?></a>
            <?php else: ?>
                <i class="bi bi-heart"></i> <?= $story->favorites()->count() ?>
            <?php endif; ?>
        </div>

        <div class="d-inline fw-bold fs-6 me-3" title="Просмотры" data-bs-toggle="tooltip">
            <i class="bi bi-eye"></i> <?= $story->reads ?>
        </div>

        <?php if ($story->user_id === getUser('id') || isAdmin()): ?>
            <div class="float-end">
                <a href="<?= route('story-edit', ['id' => $story->id]) ?>" title="Редактировать" data-bs-toggle="tooltip"><i class="bi bi-pencil"></i></a>
                <a href="<?= route('story-destroy', ['id' => $story->id]) ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete" title="Удалить" data-bs-toggle="tooltip"><i class="bi bi-x-lg"></i></a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="section shadow border p-3 mb-3" id="comments">
    <h3>Комментарии <small><?= $story->comments()->count() ?></small></h3>

    <?php if ($story->comments->isNotEmpty()): ?>
        <?php /** @var Comment $comment */ ?>
        <?php foreach ($story->comments as $comment): ?>
            <div class="post mb-3" id="comment_<?= $comment->id ?>">
                <div class="float-end text-end">
                    <?php if (getUser() && getUser('id') !== $comment->user_id): ?>
                        <a href="#" onclick="return postReply(this)" data-bs-toggle="tooltip" title="Ответить">
                            <i class="bi bi-reply text-body-secondary"></i>
                        </a>
                        <a href="#" onclick="return postQuote(this)" data-bs-toggle="tooltip" title="Цитировать">
                            <i class="bi bi-chat-quote text-body-secondary"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (isAdmin()): ?>
                        <a href="<?= route('story-comment-edit', ['id' => $story->id, 'cid' => $comment->id]) ?>"><i class="bi bi-pencil text-body-secondary"></i></a>
                        <a href="<?= route('story-comment-destroy', ['id' => $story->id, 'cid' => $comment->id]) ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete"><i class="bi bi-x-lg text-body-secondary"></i></a>
                    <?php endif; ?>

                    <div class="js-rating">
                        <?php if (getUser() && getUser('id') !== $comment->user_id): ?>
                            <a href="#" class="post-rating-down<?= $comment->poll->vote === '-' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $comment->id ?>" data-vote="-" data-type="comment" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-down"></i></a>
                        <?php endif; ?>

                        <b><?= $comment->getRating() ?></b>

                        <?php if (getUser() && getUser('id') !== $comment->user_id): ?>
                            <a href="#" class="post-rating-up<?= $comment->poll->vote === '+' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $comment->id ?>" data-vote="+" data-type="comment" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-up"></i></a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="post-author mb-1" data-login="@<?= $comment->user->login ?>">
                    <span class="avatar-micro">
                        <?= $comment->user->getAvatar() ?>
                    </span>
                    <span><?= $comment->user->getProfile() ?></span>
                </div>

                <div class="post-message">
                    <?= bbCode($comment->text) ?>
                </div>

                <small class="post-date text-body-secondary fst-italic"><?= date('d.m.Y H:i', $comment->created_at) ?></small>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill text-danger"></i>
            Комментариев еще нет!
        </div>
    <?php endif; ?>

    <?php if ($story->active): ?>
        <?php if (isUser()): ?>
            <?= $this->fetch('comments/_form', compact('story')) ?>
        <?php else: ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill text-danger"></i>
                Для выполнения действия необходимо авторизоваться!
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
