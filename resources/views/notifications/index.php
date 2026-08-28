<?php

use App\Models\Notification;
use MotorORM\Pagination;

/** @var Pagination|Notification[] $notifications */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Уведомления (Стр. <?= $notifications->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('description') ?>Уведомления (Стр. <?= $notifications->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('header') ?><h2 class="page-title">Уведомления</h2><?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Уведомления</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($notifications->isNotEmpty()): ?>
    <div class="card mb-3">
        <div class="divide-y">
            <?php /** @var Notification $notification */ ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="p-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="text-secondary"><i class="bi bi-bell"></i></span>
                        <span class="fw-bold">Уведомление</span>

                        <?php if (! $notification->read): ?>
                            <span class="badge bg-red-lt">Новое</span>
                        <?php endif; ?>

                        <span class="post-date text-secondary ms-1"><?= date('d.m.Y H:i', $notification->created_at) ?></span>

                        <a href="/notifications/<?= $notification->id ?>" class="btn btn-icon btn-sm btn-ghost-danger ms-auto" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete" title="Удалить" data-bs-toggle="tooltip">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>

                    <div class="post-message">
                        <?= $notification->getMessage() ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?= pagination($notifications) ?>
<?php else: ?>
    <?= $this->fetch('app/_empty', [
        'title'    => 'Уведомлений нет',
        'subtitle' => 'Здесь появятся ответы на ваши сообщения и упоминания',
        'icon'     => 'bi-bell',
    ]) ?>
<?php endif; ?>
