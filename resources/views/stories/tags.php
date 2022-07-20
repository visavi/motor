<?php
/** @var array<string, int> $links */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Теги<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Теги</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php foreach ($links as $link => $size): ?>
    <a href="/tags/<?= $link ?>"><span style="font-size:<?= $size ?>pt"><?= $link ?></span></a>
<?php endforeach; ?>
