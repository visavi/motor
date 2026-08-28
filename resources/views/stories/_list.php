<?php

use App\Models\Story;
use MotorORM\Pagination;

/** @var Pagination|Story[] $stories */
/** @var string|null $emptyTitle */
/** @var string|null $emptySubtitle */

$emptyTitle ??= 'Статей ещё нет';
$emptySubtitle ??= 'Как только появится первая статья, она окажется здесь';
?>

<?php if ($stories->isNotEmpty()): ?>
    <div class="card mb-3">
        <div class="divide-y">
            <?php foreach ($stories as $story): ?>
                <article class="p-3">
                    <div class="row align-items-start">
                        <div class="col-auto js-rating text-center" style="width: 3.5rem">
                            <?php if (getUser() && getUser('id') !== $story->user_id): ?>
                                <a href="#" class="d-block post-rating-up<?= $story->poll->vote === '+' ? ' active' : '' ?>" onclick="return changeRating(this);" data-id="<?= $story->id ?>" data-vote="+" data-type="story" data-csrf="<?= session('csrf') ?>" aria-label="Плюс">
                                    <i class="bi bi-caret-up-fill"></i>
                                </a>
                            <?php endif; ?>

                            <b class="d-block fs-3"><?= $story->getRating() ?></b>

                            <?php if (getUser() && getUser('id') !== $story->user_id): ?>
                                <a href="#" class="d-block post-rating-down<?= $story->poll->vote === '-' ? ' active' : '' ?>" onclick="return changeRating(this);" data-id="<?= $story->id ?>" data-vote="-" data-type="story" data-csrf="<?= session('csrf') ?>" aria-label="Минус">
                                    <i class="bi bi-caret-down-fill"></i>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="col">
                            <div class="d-flex align-items-start gap-2">
                                <h3 class="mb-1 me-auto">
                                    <a href="<?= $story->getLink() ?>" class="text-reset"><?= escape($story->title) ?></a>
                                    <?php if ($story->locked): ?>
                                        <i class="bi bi-pin-angle text-secondary" title="Закреплена" data-bs-toggle="tooltip"></i>
                                    <?php endif; ?>
                                </h3>

                                <?php if ($story->user_id === getUser('id') || isAdmin()): ?>
                                    <div class="btn-list flex-nowrap">
                                        <a href="<?= route('story-edit', ['id' => $story->id]) ?>" class="btn btn-icon btn-sm btn-ghost-secondary" title="Редактировать" data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= route('story-destroy', ['id' => $story->id]) ?>" class="btn btn-icon btn-sm btn-ghost-danger" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete" title="Удалить" data-bs-toggle="tooltip">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex align-items-center flex-wrap gap-2 text-secondary small mb-2">
                                <span class="post-author">
                                    <?= $story->user->getAvatar('xs') ?>
                                    <span><?= $story->user->getProfile() ?></span>
                                </span>
                                <span>&middot;</span>
                                <span><?= date('d.m.Y H:i', $story->created_at) ?></span>

                                <?php if ($story->active === false && ($story->user_id === getUser('id') || isAdmin())): ?>
                                    <span class="badge bg-red-lt">Не опубликовано</span>
                                <?php endif; ?>

                                <?php if ($story->created_at > time() && isAdmin()): ?>
                                    <span class="badge bg-yellow-lt">Отложенная публикация</span>
                                <?php endif; ?>
                            </div>

                            <div class="post-message">
                                <?= $story->shortText(setting('story.short_words')) ?>
                            </div>

                            <?php if ($story->tags->isNotEmpty()): ?>
                                <div class="btn-list mt-2">
                                    <?php foreach ($story->tags as $tag): ?>
                                        <a href="/tags/<?= urlencode($tag->tag) ?>" class="badge bg-blue-lt"><?= escape($tag->tag) ?></a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex align-items-center gap-3 mt-2 text-secondary">
                                <a href="<?= $story->getLink() ?>#comments" class="text-reset" title="Комментарии" data-bs-toggle="tooltip">
                                    <i class="bi bi-chat"></i> <?= count($story->comments) ?>
                                </a>

                                <?php if (isUser()): ?>
                                    <a href="#" class="text-reset" onclick="return addFavorite(this);" data-id="<?= $story->id ?>" data-csrf="<?= session('csrf') ?>" title="Избранное" data-bs-toggle="tooltip">
                                        <i class="bi <?= $story->favorite->id ? 'bi-heart-fill' : 'bi-heart' ?>"></i> <?= count($story->favorites) ?>
                                    </a>
                                <?php else: ?>
                                    <span title="Избранное" data-bs-toggle="tooltip">
                                        <i class="bi bi-heart"></i> <?= count($story->favorites) ?>
                                    </span>
                                <?php endif; ?>

                                <span title="Просмотры" data-bs-toggle="tooltip">
                                    <i class="bi bi-eye"></i> <?= $story->reads ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

    <?= pagination($stories) ?>
<?php else: ?>
    <?= $this->fetch('app/_empty', [
        'title'    => $emptyTitle,
        'subtitle' => $emptySubtitle,
        'icon'     => 'bi-card-heading',
        'action'   => isUser() && (setting('story.allow_posting') || isAdmin()) ? 'Написать статью' : null,
        'link'     => route('story-create'),
    ]) ?>
<?php endif; ?>
