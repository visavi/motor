<?php

use App\Models\File;
use App\Models\Story;
use MotorORM\Collection;

/** @var Story $post */
/** @var Collection<File> $files */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Редактирование статьи<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="/<?= $post->slug ?>-<?= $post->id ?>"><?= $this->e($post->title) ?></a></li>
            <li class="breadcrumb-item active">Редактирование</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?= $this->fetch('stories/_form', compact('post', 'files')) ?>
