<?php

use App\Models\Comment;
use App\Repositories\CommentRepository;
use App\Repositories\StoryRepository;

/** @var Comment $comment */
?>

<div class="section shadow p-3 mb-3">
    <h5>Активность</h5>
    <?php $commentRepository = new CommentRepository(); ?>

    <ul class="nav nav-tabs nav-fill" id="commentsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="last-comment-tab" data-bs-toggle="tab" data-bs-target="#last-comment-tab-pane" type="button" role="tab" aria-controls="last-comment-tab-pane" aria-selected="true">Последние</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="best-comment-tab" data-bs-toggle="tab" data-bs-target="#best-comment-tab-pane" type="button" role="tab" aria-controls="best-comment-tab-pane" aria-selected="false">Лучшие</button>
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

<div class="section shadow p-3 mb-3">
    <h5>Теги</h5>

    <?php $storyRepository = new StoryRepository(); ?>

    <?php foreach ($storyRepository->getPopularTags() as $tag => $count): ?>
        <a href="/tags/<?= $tag ?>" class="badge bg-secondary"><?= $tag ?></a>
    <?php endforeach; ?>

    <div class="mt-3">
        <a href="/tags">Показать все теги</a>
    </div>
</div>
