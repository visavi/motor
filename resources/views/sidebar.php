<?php

use App\Models\Comment;
use App\Repositories\CommentRepository;
use App\Repositories\StoryRepository;

/** @var Comment $comment */
?>

<div class="section shadow p-3 mb-3">
    <h5>Активность</h5>

    <?php $commentRepository = new CommentRepository(); ?>

    <?php foreach ($commentRepository->getLastComments() as $comment): ?>
        <div class="mb-3 border-bottom">
            <div class="section-author">
                    <span class="avatar-micro">
                        <?= $comment->user->getAvatar() ?>
                    </span>
                <span><?= $comment->user->getName() ?></span>
            </div>

            <div class="section-post">
                <?= $comment->shortText() ?>
                <small class="text-muted fst-italic ms-1"><?= date('d.m.Y H:i', $comment->created_at) ?></small>
            </div>

            <div>
                <a href="<?= $comment->story->getLink() ?>"><i class="bi bi-sticky"></i> <?= $this->e($comment->story->title) ?></a>
            </div>
        </div>
    <?php endforeach; ?>
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
