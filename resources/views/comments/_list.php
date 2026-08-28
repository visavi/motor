<?php

use App\Models\Comment;
use MotorORM\Collection;

/** @var Collection<Comment> $comments */
?>

<?php if ($comments->isNotEmpty()): ?>
    <div class="divide-y">
        <?php foreach ($comments as $comment): ?>
            <div class="p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="post-author">
                        <?= $comment->user->getAvatar('xs') ?>
                        <span><?= escape($comment->user->getName()) ?></span>
                    </span>
                    <b class="ms-auto"><?= $comment->getRating() ?></b>
                </div>

                <div class="text-secondary small">
                    <?= $comment->shortText() ?>
                </div>

                <div class="d-flex align-items-center gap-2 mt-1 small">
                    <a href="<?= $comment->story->getLink() ?>" class="text-truncate">
                        <i class="bi bi-sticky"></i> <?= escape($comment->story->title) ?>
                    </a>
                    <span class="text-secondary ms-auto text-nowrap"><?= date('d.m.Y H:i', $comment->created_at) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="p-3 text-secondary text-center">Комментариев пока нет</div>
<?php endif; ?>
