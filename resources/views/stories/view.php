<?php

use App\Models\File;
use App\Models\Story;
use MotorORM\Collection;
use MotorORM\CollectionPaginate;

/** @var CollectionPaginate|Story[] $posts */
/** @var Collection|File[] $files */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?><?= $this->e($post->title) ?><?php $this->stop() ?>
<?php $this->start('description') ?><?= $this->e($post->title) ?><?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item active"><?= $this->e($post->title) ?></li>
    </ol>
</nav>
<?php $this->stop() ?>

<article class="shadow p-3 mb-3">
    <?php if (isAdmin()): ?>
        <div class="float-end">
            <a href="/<?= $post->id ?>/edit"><i class="bi bi-pencil"></i></a>
            <a href="#" onclick="return submitForm(this);">
                <i class="bi bi-x-lg"></i>
                <form action="/<?= $post->id ?>" method="post" style="display:none">
                    <input type="hidden" name="_METHOD" value="DELETE">
                    <input type="hidden" name="csrf" value="<?= session()->get('csrf') ?>">
                </form>
            </a>
        </div>
    <?php endif; ?>

    <div class="message">
        <?= bbCode($post->text) ?>
    </div>
    <div class="section-author">
        <?php if ($post->user()): ?>
            <span class="avatar-micro">
                <?= $post->user()->getAvatar() ?>
            </span>
            <span><a href="/users/<?= $post->user()->login ?>"><?= $post->user()->getName() ?></a></span>
        <?php else: ?>
            <span class="avatar-micro">
                <img class="avatar-default rounded-circle" src="/assets/images/avatar_default.png" alt="Аватар">
            </span>
            <span><?= setting('main.delete_name') ?></span>
        <?php endif; ?>

        <small class="text-muted fst-italic ms-1"><?= date('d.m.Y H:i', $post->created_at) ?></small>
    </div>
</article>
