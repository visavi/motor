<?php

use App\Models\File;
use MotorORM\Collection;

/** @var Collection<File> $files */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Добавление статьи<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Добавление</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if(setting('story.allow_posting') || isAdmin()): ?>
    <?= $this->fetch('stories/_form', compact('files')) ?>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Публикация статей запрещена администратором!
    </div>
<?php endif; ?>
