<?php

use App\Entities\Story;
use App\Services\Pagination;

/** @var Story $story */
/** @var Pagination $pagination */
?>

<?php if ($pagination->count()): ?>
    <?php foreach ($pagination->items() as $story): ?>
        <article class="section shadow border p-3 mb-3">
            <div class="float-end js-rating">
                <?php if ($story->getActive() === false && ($story->getUserId() === getUser('id') || isAdmin())): ?>
                    <span class="badge text-bg-danger">Не опубликовано</span>
                <?php endif; ?>

                <?php if ($story->getCreatedAt() > new Datetime('now') && isAdmin()): ?>
                    <span class="badge text-bg-warning">Отложенная публикация</span>
                <?php endif; ?>

                <?php if (getUser() && getUser('id') !== $story->getUserId()): ?>
                    <a href="#" class="post-rating-down<?= $story->poll->vote === '-' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $story->getId() ?>" data-vote="-" data-type="story" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-down"></i></a>
                <?php endif; ?>

                <b><?= $story->getRating() ?></b>

                <?php if (getUser() && getUser('id') !== $story->getUserId()): ?>
                    <a href="#" class="post-rating-up<?= $story->poll->vote === '+' ? ' active': '' ?>" onclick="return changeRating(this);" data-id="<?= $story->getId() ?>" data-vote="+" data-type="story" data-csrf="<?= session('csrf') ?>"><i class="bi bi-arrow-up"></i></a>
                <?php endif; ?>
            </div>

            <h3>
                <a href="<?php /*= $story->getLink()*/ ?>"><?= escape($story->getTitle()) ?></a>
                <?php if ($story->getLocked()): ?>
                    <small><i class="bi bi-pin-angle"></i></small>
                <?php endif; ?>
            </h3>

            <div class="post-message">
                <?php /*= $story->shortText(setting('story.short_words'))*/ ?>
            </div>

            <div class="post-author d-inline-block mt-3">
                <span class="avatar-micro">
                    <?php /*= $story->user->getAvatar()*/ ?>
                </span>
                <span><?php /*= $story->user->getProfile()*/ ?></span>
            </div>

            <small class="post-date text-body-secondary fst-italic ms-1">
                <?= $story->getCreatedAt()->format('d.m.Y H:i') ?>
            </small>

            <div class="my-3 fst-italic">
                <i class="bi bi-tags"></i> <?php /*= $story->getTags()*/ ?>
            </div>

            <div class="border rounded p-2">
                <div class="d-inline fw-bold fs-6 me-3" title="Комментарии" data-bs-toggle="tooltip">
                    <a href="<?php /*= $story->getLink()*/ ?>#comments"><i class="bi bi-chat"></i> <?php /*= $story->comments()->count()*/ ?></a>
                </div>

                <div class="d-inline fw-bold fs-6 me-3" title="Избранное" data-bs-toggle="tooltip">
                    <?php if (isUser()): ?>
                        <a href="#" onclick="return addFavorite(this);" data-id="<?= $story->getId() ?>"  data-csrf="<?= session('csrf') ?>"><i class="bi <?= /*$story->favorite->id*/ false ? 'bi-heart-fill' : 'bi-heart' ?>"></i> <?php /*= $story->favorites()->count()*/ ?></a>
                    <?php else: ?>
                        <i class="bi bi-heart"></i> <?php /*= $story->favorites()->count()*/ ?>
                    <?php endif; ?>
                </div>

                <div class="d-inline fw-bold fs-6 me-3" title="Просмотры" data-bs-toggle="tooltip">
                    <i class="bi bi-eye"></i> <?= $story->getReads() ?>
                </div>

                <?php if ($story->getUserId() === getUser('id') || isAdmin()): ?>
                    <div class="float-end ms-3">
                        <!-- <i class="bi bi-three-dots-vertical"></i> -->

                        <a href="<?= route('story-edit', ['id' => $story->getId()]) ?>" title="Редактировать" data-bs-toggle="tooltip"><i class="bi bi-pencil"></i></a>
                        <a href="<?= route('story-destroy', ['id' => $story->getId()]) ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete" title="Удалить" data-bs-toggle="tooltip"><i class="bi bi-x-lg"></i></a>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>

    <?= $pagination->links() ?>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Статей еще нет!
    </div>
<?php endif; ?>
