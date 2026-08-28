<?php

use App\Models\Comment;
use App\Repositories\CommentRepository;
use App\Repositories\GuestbookRepository;
use App\Repositories\StoryRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;

/** @var Comment $comment */

$storyRepository = new StoryRepository();
$guestbookRepository = new GuestbookRepository();
$userRepository = new UserRepository();
$commentRepository = new CommentRepository();
$tagRepository = new TagRepository();
?>

<div class="card mb-3">
    <div class="list-group list-group-flush">
        <a href="<?= route('stories') ?>" class="list-group-item list-group-item-action d-flex align-items-center">
            <span class="text-secondary me-2"><i class="bi bi-card-heading"></i></span>
            Статьи
            <span class="badge bg-blue-lt ms-auto"><?= $storyRepository->getCount() ?></span>
        </a>
        <a href="<?= route('guestbook') ?>" class="list-group-item list-group-item-action d-flex align-items-center">
            <span class="text-secondary me-2"><i class="bi bi-chat-square-text"></i></span>
            Гостевая книга
            <span class="badge bg-blue-lt ms-auto"><?= $guestbookRepository->getCount() ?></span>
        </a>
        <a href="<?= route('users') ?>" class="list-group-item list-group-item-action d-flex align-items-center">
            <span class="text-secondary me-2"><i class="bi bi-people"></i></span>
            Пользователи
            <span class="badge bg-blue-lt ms-auto"><?= $userRepository->getCount() ?></span>
        </a>
        <a href="https://github.com/visavi/motor" class="list-group-item list-group-item-action d-flex align-items-center" rel="noopener" target="_blank">
            <span class="text-secondary me-2"><i class="bi bi-github"></i></span>
            Motor
        </a>
        <a href="https://github.com/visavi/motor-orm" class="list-group-item list-group-item-action d-flex align-items-center" rel="noopener" target="_blank">
            <span class="text-secondary me-2"><i class="bi bi-github"></i></span>
            Motor ORM
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#last-comment-tab-pane" type="button" role="tab" aria-selected="true">Последние</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#best-comment-tab-pane" type="button" role="tab" aria-selected="false">Популярные</button>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="last-comment-tab-pane" role="tabpanel" tabindex="0">
                <?= $this->fetch('comments/_list', ['comments' => $commentRepository->getLastComments()]) ?>
            </div>
            <div class="tab-pane fade" id="best-comment-tab-pane" role="tabpanel" tabindex="0">
                <?= $this->fetch('comments/_list', ['comments' => $commentRepository->getBestComments()]) ?>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">Теги</h3>
    </div>
    <div class="card-body">
        <div class="btn-list">
            <?php foreach ($tagRepository->getPopularTags() as $tag => $count): ?>
                <a href="/tags/<?= urlencode($tag) ?>" class="badge bg-blue-lt"><?= escape($tag) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="card-footer">
        <a href="/tags">Показать все теги</a>
    </div>
</div>
