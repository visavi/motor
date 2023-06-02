<?php

use App\Models\Notification;
use MotorORM\CollectionPaginate;

/** @var CollectionPaginate|Notification[] $notifications */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Уведомления (Стр. <?= $notifications->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('description') ?>Уведомления (Стр. <?= $notifications->currentPage() ?>)<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Уведомления</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($notifications->isNotEmpty()): ?>
    <div class="section shadow border p-3 mb-3">
        <?php /** @var Notification $notification */ ?>
        <?php foreach ($notifications as $notification): ?>
            <div class="post mb-3">
                <div class="float-end text-end">
                    <a href="/notifications/<?= $notification->id ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete"><i class="bi bi-x-lg text-body-secondary"></i></a>
                </div>

                <div class="post-author">
                    <i class="bi bi-bell-fill"></i> Уведомление

                    <?php if (! $notification->read): ?>
                        <span class="badge text-bg-danger">Новое</span>
                    <?php endif; ?>
                </div>

                <div class="post-message">
                    <?= bbCode($notification->message) ?>
                </div>

                <small class="post-date text-body-secondary fst-italic"><?= date('d.m.Y H:i', $notification->created_at) ?></small>
            </div>
        <?php endforeach; ?>
    </div>
    <?= $notifications->links() ?>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Уведомлений еще нет!
    </div>
<?php endif; ?>
