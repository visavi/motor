<?php

use App\Models\Comment;
use App\Models\Story;

/** @var Story $story */
/** @var Comment|null $comment */
?>

<div class="post mb-3<?php if ($comment->parent_id): ?> border-start<?php endif;?>" id="comment_<?= $comment->id ?>" style="padding-left: 10px; margin-left: <?= $comment->depth * 20 ?>px;" data-depth="<?= $comment->depth ?>" data-parent="<?= $comment->parent_id ?>">
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
        <?= $comment->getText() ?>
    </div>

    <small class="post-date text-body-secondary fst-italic"><?= date('d.m.Y H:i', $comment->created_at) ?></small>
</div>

<?php if (isset($comment->child)): ?>
    <?php foreach ($comment->child as $child): ?>
        <?= $this->fetch('comments/_comment', ['story' => $story, 'comment' => $child]) ?>
    <?php endforeach; ?>
<?php endif; ?>
