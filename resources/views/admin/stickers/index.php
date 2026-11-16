<?php

use App\Models\Sticker;
use MotorORM\Pagination;

/** @var Pagination|Sticker[] $stickers */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Стикеры<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('admin') ?>">Админ-панель</a></li>
            <li class="breadcrumb-item active">Cтикеры</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($stickers->isNotEmpty()): ?>
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Стикеры</h3>
            <div class="card-actions">
                <span class="badge bg-secondary-lt"><?= $stickers->total() ?></span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th class="w-1">Стикер</th>
                        <th>Код</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stickers as $sticker): ?>
                        <tr>
                            <td><img src="<?= $sticker->path ?>" alt="<?= escape($sticker->code) ?>" style="max-height: 32px"></td>
                            <td><code><?= escape($sticker->code) ?></code></td>
                            <td>
                                <div class="btn-list flex-nowrap justify-content-end">
                                    <a href="<?= route('admin-sticker-edit', ['id' => $sticker->id]) ?>" class="btn btn-icon btn-sm btn-ghost-secondary" title="Редактировать" data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?= route('admin-sticker-destroy', ['id' => $sticker->id]) ?>" class="btn btn-icon btn-sm btn-ghost-danger" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete" title="Удалить" data-bs-toggle="tooltip">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?= pagination($stickers) ?>
<?php else: ?>
    <?= $this->fetch('app/_empty', [
        'title'    => 'Стикеров ещё нет',
        'subtitle' => 'Загрузите первый стикер, чтобы он появился в списке',
        'icon'     => 'bi-emoji-smile',
    ]) ?>
<?php endif; ?>

<?= $this->fetch('admin/stickers/_form') ?>
