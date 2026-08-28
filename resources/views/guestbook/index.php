<?php

use App\Models\Guestbook;
use MotorORM\Pagination;

/** @var Pagination|<Guestbook[] $messages */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Гостевая книга (Стр. <?= $messages->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('description') ?>Гостевая книга (Стр. <?= $messages->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('header') ?><h2 class="page-title">Гостевая книга</h2><?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Гостевая книга</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($messages->isNotEmpty()): ?>
    <div class="card mb-3">
        <div class="divide-y">
            <?php /** @var Guestbook $message */ ?>
            <?php foreach ($messages as $message): ?>
                <div class="post p-3">
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <?php if ($message->user->id): ?>
                            <div class="post-author" data-login="@<?= $message->user->login ?>">
                                <?= $message->user->getAvatar() ?>
                                <a href="/users/<?= $message->user->login ?>" class="text-reset"><?= escape($message->user->getName()) ?></a>
                            </div>
                        <?php else: ?>
                            <div class="post-author" data-login="<?= $message->name ?? setting('main.guest_name') ?>">
                                <span class="avatar avatar-sm" style="background-image: url(/assets/images/avatar_default.png)"></span>
                                <span><?= escape($message->name ?? setting('main.guest_name')) ?></span>
                                <span class="badge bg-secondary-lt">гость</span>
                            </div>
                        <?php endif; ?>

                        <span class="post-date text-secondary ms-1"><?= date('d.m.Y H:i', $message->created_at) ?></span>

                        <div class="ms-auto btn-list flex-nowrap">
                            <?php if ($message->active === false): ?>
                                <span class="badge bg-red-lt">Не опубликовано</span>
                            <?php elseif (getUser() && getUser('id') !== $message->user_id): ?>
                                <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary" onclick="return postReply(this)" title="Ответить" data-bs-toggle="tooltip">
                                    <i class="bi bi-reply"></i>
                                </a>
                                <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary" onclick="return postQuote(this)" title="Цитировать" data-bs-toggle="tooltip">
                                    <i class="bi bi-chat-quote"></i>
                                </a>
                            <?php endif; ?>

                            <?php if (isAdmin()): ?>
                                <a href="/guestbook/<?= $message->id ?>/edit" class="btn btn-icon btn-sm btn-ghost-secondary" title="Редактировать" data-bs-toggle="tooltip">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/guestbook/<?= $message->id ?>" class="btn btn-icon btn-sm btn-ghost-danger" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete" title="Удалить" data-bs-toggle="tooltip">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="post-message">
                        <?= $message->getText() ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?= pagination($messages) ?>
<?php else: ?>
    <?= $this->fetch('app/_empty', [
        'title'    => 'Сообщений ещё нет',
        'subtitle' => 'Оставьте первое — форма ниже',
        'icon'     => 'bi-chat-square-text',
    ]) ?>
<?php endif; ?>

<?php if (isUser() || setting('guestbook.allow_guests')): ?>
    <?= $this->insert('guestbook/_form') ?>
<?php else: ?>
    <div class="alert alert-warning" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i>
        Для выполнения действия необходимо авторизоваться
    </div>
<?php endif; ?>
