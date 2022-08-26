<?php

use App\Models\Comment;
use MotorORM\Collection;

/** @var Collection<Comment> $comments */
?>

<?php foreach ($comments as $comment): ?>
    <div class="my-3 border-bottom">
        <div class="float-end">
            <b><?= $comment->getRating() ?></b>
        </div>

        <div class="post-author">
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
