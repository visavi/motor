<?php

use App\Models\Comment;
use App\Repositories\CommentRepository;
use App\Repositories\GuestbookRepository;
use App\Repositories\StoryRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;

/** @var Comment $comment */
?>

<div class="list-group shadow mb-3">
    <?php $storyRepository = new StoryRepository(); ?>
    <a href="<?= route('stories') ?>" class="list-group-item list-group-item-action fw-bold">
        <i class="bi bi-card-heading"></i>
        Статьи <span class="badge bg-primary rounded-pill float-end"><?= $storyRepository->getCount() ?></span>
    </a>

    <?php $guestbookRepository = new GuestbookRepository(); ?>
    <a href="<?= route('guestbook') ?>" class="list-group-item list-group-item-action fw-bold">
        <i class="bi bi-chat-square-text-fill"></i>
        Гостевая книга <span class="badge bg-primary rounded-pill float-end"><?= $guestbookRepository->getCount() ?></span>
    </a>

    <?php $userRepository = new UserRepository(); ?>
    <a href="<?= route('users') ?>" class="list-group-item list-group-item-action fw-bold">
        <i class="bi bi-people-fill"></i>
        Пользователи <span class="badge bg-primary rounded-pill float-end"><?= $userRepository->getCount() ?></span>
    </a>

    <a href="https://github.com/visavi/motor" class="list-group-item list-group-item-action fw-bold">
        <i class="bi bi-github"></i> Motor
    </a>

    <a href="https://github.com/visavi/motor-orm" class="list-group-item list-group-item-action fw-bold">
        <i class="bi bi-github"></i> Motor ORM
    </a>
</div>

<div class="section shadow border p-3 mb-3">
    <h5>Активность</h5>
    <?php $commentRepository = new CommentRepository(); ?>

    <ul class="nav nav-tabs nav-fill" id="commentsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="last-comment-tab" data-bs-toggle="tab" data-bs-target="#last-comment-tab-pane" type="button" role="tab" aria-controls="last-comment-tab-pane" aria-selected="true">Последние</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="best-comment-tab" data-bs-toggle="tab" data-bs-target="#best-comment-tab-pane" type="button" role="tab" aria-controls="best-comment-tab-pane" aria-selected="false">Популярные</button>
        </li>
    </ul>
    <div class="tab-content" id="commentsTabContent">
        <div class="tab-pane fade show active" id="last-comment-tab-pane" role="tabpanel" aria-labelledby="last-comment-tab" tabindex="0">
            <?= $this->fetch('comments/_list', ['comments' => $commentRepository->getLastComments()]) ?>
        </div>
        <div class="tab-pane fade" id="best-comment-tab-pane" role="tabpanel" aria-labelledby="best-comment-tab" tabindex="0">
            <?= $this->fetch('comments/_list', ['comments' => $commentRepository->getBestComments()]) ?>
        </div>
    </div>
</div>

<div class="section shadow border p-3 mb-3">
    <h5>Теги</h5>

    <?php $tagRepository = new TagRepository(); ?>

    <?php foreach ($tagRepository->getPopularTags() as $tag => $count): ?>
        <a href="/tags/<?= $tag ?>" class="badge bg-primary"><?= $tag ?></a>
    <?php endforeach; ?>

    <div class="mt-3">
        <a href="/tags">Показать все теги</a>
    </div>
</div>
