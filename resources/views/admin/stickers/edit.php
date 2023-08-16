<?php

use App\Models\Sticker;

/** @var Sticker $sticker */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Редактирование<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('admin') ?>">Админ-панель</a></li>
            <li class="breadcrumb-item"><a href="<?= route('admin-stickers') ?>">Cтикеры</a></li>
            <li class="breadcrumb-item active">Редактирование</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?= $this->fetch('admin/stickers/_form', compact('sticker')) ?>
