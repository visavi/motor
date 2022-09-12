<?php

use App\Models\Guestbook;
use MotorORM\CollectionPaginate;

/** @var CollectionPaginate|<Guestbook[] $messages */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Гостевая книга (Стр. <?= $messages->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('description') ?>Гостевая книга (Стр. <?= $messages->currentPage() ?>)<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Гостевая книга</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($messages->isNotEmpty()): ?>
    <?php /** @var Guestbook $comment */ ?>
    <?php foreach ($messages as $message): ?>
        <div class="post shadow p-3 mb-3">
            <div class="float-end text-end">
                <?php if (getUser() && getUser('id') !== $message->user_id): ?>
                    <a href="#" onclick="return postReply(this)" data-bs-toggle="tooltip" title="Ответить">
                        <i class="bi bi-reply text-muted"></i>
                    </a>
                    <a href="#" onclick="return postQuote(this)" data-bs-toggle="tooltip" title="Цитировать">
                        <i class="bi bi-chat-quote text-muted"></i>
                    </a>
                <?php endif; ?>
            </div>

            <?php if (isAdmin()): ?>
                <div class="float-end">
                    <a href="/guestbook/<?= $message->id ?>/edit"><i class="bi bi-pencil"></i></a>
                    <a href="/guestbook/<?= $message->id ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete"><i class="bi bi-x-lg"></i></a>
                </div>
            <?php endif; ?>


            <?php if ($message->user->id): ?>
                <div class="post-author" data-login="<?= $message->user->getName() ?>">
                    <span class="avatar-micro">
                        <?= $message->user->getAvatar() ?>
                    </span>
                    <span><a href="/users/<?= $message->user->login ?>"><?= $message->user->getName() ?></a></span>
                </div>
            <?php else: ?>
                <div class="post-author" data-login="<?= $message->name ?? setting('main.guest_name') ?>">
                    <span class="avatar-micro">
                        <img class="avatar-default rounded-circle" src="/assets/images/avatar_default.png" alt="Аватар">
                    </span>
                    <span><?= $message->name ?? setting('main.guest_name') ?></span>
                </div>
            <?php endif; ?>

            <div class="post-message">
                <?= bbCode($message->text) ?>
            </div>

            <small class="post-date text-muted fst-italic ms-1"><?= date('d.m.Y H:i', $message->created_at) ?></small>
        </div>
    <?php endforeach; ?>

    <?= $messages->links() ?>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Сообщений еще нет!
    </div>
<?php endif; ?>

<?php if (isUser() || setting('guestbook.allow_guests')): ?>
    <?= $this->insert('guestbook/_form') ?>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Для выполнения действия необходимо авторизоваться!
    </div>
<?php endif; ?>
