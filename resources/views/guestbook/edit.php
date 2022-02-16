<?php
use App\Models\Guestbook;

/** @var Guestbook $message */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Редактирование - Гостевая книга<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="/guestbook">Гостевая книга</a></li>
            <li class="breadcrumb-item active">Редактирование</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?= $this->fetch('guestbook/_form', compact('message')) ?>
