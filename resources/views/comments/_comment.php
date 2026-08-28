<?php

use App\Models\Comment;
use App\Models\Story;

/** @var Story $story */
/** @var Comment|null $comment */
?>

<div class="post p-3<?= $comment->parent_id ? ' border-start border-2' : '' ?>" id="comment_<?= $comment->id ?>" style="margin-left: <?= $comment->depth * 20 ?>px" data-depth="<?= $comment->depth ?>" data-parent="<?= $comment->parent_id ?>">
    <div class="d-flex align-items-start gap-2 mb-2">
        <div class="post-author" data-login="@<?= $comment->user->login ?>">
            <?= $comment->user->getAvatar() ?>
            <span><?= $comment->user->getProfile() ?></span>
        </div>

        <span class="post-date text-secondary ms-1"><?= date('d.m.Y H:i', $comment->created_at) ?></span>

        <div class="ms-auto d-flex align-items-center gap-1">
            <?php if (getUser() && getUser('id') !== $comment->user_id): ?>
                <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary" onclick="return postReply(this)" title="Ответить" data-bs-toggle="tooltip">
                    <i class="bi bi-reply"></i>
                </a>
                <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary" onclick="return postQuote(this)" title="Цитировать" data-bs-toggle="tooltip">
                    <i class="bi bi-chat-quote"></i>
                </a>
            <?php endif; ?>

            <?php if (isAdmin()): ?>
                <a href="<?= route('story-comment-edit', ['id' => $story->id, 'cid' => $comment->id]) ?>" class="btn btn-icon btn-sm btn-ghost-secondary" title="Редактировать" data-bs-toggle="tooltip">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="<?= route('story-comment-destroy', ['id' => $story->id, 'cid' => $comment->id]) ?>" class="btn btn-icon btn-sm btn-ghost-danger" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete" title="Удалить" data-bs-toggle="tooltip">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>

            <div class="js-rating d-flex align-items-center gap-1 ms-1">
                <?php if (getUser() && getUser('id') !== $comment->user_id): ?>
                    <a href="#" class="post-rating-down<?= $comment->poll->vote === '-' ? ' active' : '' ?>" onclick="return changeRating(this);" data-id="<?= $comment->id ?>" data-vote="-" data-type="comment" data-csrf="<?= session('csrf') ?>" aria-label="Минус">
                        <i class="bi bi-caret-down-fill"></i>
                    </a>
                <?php endif; ?>

                <b><?= $comment->getRating() ?></b>

                <?php if (getUser() && getUser('id') !== $comment->user_id): ?>
                    <a href="#" class="post-rating-up<?= $comment->poll->vote === '+' ? ' active' : '' ?>" onclick="return changeRating(this);" data-id="<?= $comment->id ?>" data-vote="+" data-type="comment" data-csrf="<?= session('csrf') ?>" aria-label="Плюс">
                        <i class="bi bi-caret-up-fill"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="post-message">
        <?= $comment->getText() ?>
    </div>
</div>

<?php if (isset($comment->child)): ?>
    <?php foreach ($comment->child as $child): ?>
        <?= $this->fetch('comments/_comment', ['story' => $story, 'comment' => $child]) ?>
    <?php endforeach; ?>
<?php endif; ?>
