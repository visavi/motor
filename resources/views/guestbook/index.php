<?php

use App\Models\File;
use App\Models\Guestbook;
use MotorORM\Collection;
use MotorORM\CollectionPaginate;

/** @var CollectionPaginate|Guestbook[] $messages */
/** @var Collection|File[] $files */
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
    <?php foreach ($messages as $message): ?>
        <div class="shadow p-3 mb-3">
            <?php if (isAdmin()): ?>
                <div class="float-end">
                    <a href="/guestbook/<?= $message->id ?>/edit"><i class="bi bi-pencil"></i></a>
                    <a href="#" onclick="return (confirm('Подтвердите удаление!')) ? $(this).find('form').submit() : false;">
                        <i class="bi bi-x-lg"></i>
                        <form action="/guestbook/<?= $message->id ?>" method="post" style="display:none">
                            <input type="hidden" name="_METHOD" value="DELETE">
                            <input type="hidden" name="csrf" value="<?= session()->get('csrf') ?>">
                        </form>
                    </a>
                </div>
            <?php endif; ?>

            <h5><?= $this->e($message->title) ?></h5>

            <div class="message">
                <?= bbCode($message->text) ?>
            </div>
            <div class="section-author">
                <?php if ($message->user()): ?>
                    <span class="avatar-micro">
                        <?= $message->user()->getAvatar() ?>
                    </span>
                    <span><a href="/users/<?= $message->user()->login ?>"><?= $message->user()->login ?></a></span>
                <?php else: ?>
                    <span class="avatar-micro">
                        <img class="avatar-default rounded-circle" src="/assets/images/avatar_default.png" alt="Аватар">
                    </span>
                    <span><?= setting('main.guest_name') ?></span>
                <?php endif; ?>

                <small class="text-muted fst-italic ms-1"><?= date('d.m.Y H:i', $message->created_at) ?></small>
            </div>
        </div>
    <?php endforeach; ?>

    <?= $messages->links() ?>
<?php else: ?>
    <div class="alert alert-danger">Сообщений еще нет!</div>
<?php endif; ?>

<?php if (isUser() || setting('guestbook.allow_guests')): ?>
    <?= $this->insert('guestbook/_form', compact('files')) ?>
<?php else: ?>
    <div class="alert alert-danger">Авторизуйтесь для добавления сообщений</div>
<?php endif; ?>
