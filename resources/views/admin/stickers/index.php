<?php

use App\Models\Sticker;
use MotorORM\CollectionPaginate;

/** @var CollectionPaginate|Sticker[] $stickers */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Логи<?php $this->stop() ?>

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
    <?php foreach ($stickers as $sticker): ?>
        <div class="section shadow border p-3 mb-3">
            <img src="<?= $sticker->path ?>" alt=""> <b><?= $sticker->code ?></b>

            <div class="float-end ms-3">
                <a href="<?= route('admin-sticker-edit', ['id' => $sticker->id]) ?>" title="Редактировать" data-bs-toggle="tooltip"><i class="bi bi-pencil"></i></a>
                <a href="<?= route('admin-sticker-destroy', ['id' => $sticker->id]) ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete" title="Удалить" data-bs-toggle="tooltip"><i class="bi bi-x-lg"></i></a>
            </div>
        </div>
    <?php endforeach; ?>

     <?= $stickers->links() ?>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Стикеров еще нет!
    </div>
<?php endif; ?>

<?= $this->fetch('admin/stickers/_form') ?>
