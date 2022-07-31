<?php
/** @var array<string, int> $tags */
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

<?php foreach ($tags as $tag => $size): ?>
    <a href="/tags/<?= $tag ?>"><span style="font-size:<?= $size ?>pt"><?= $tag ?></span></a>
<?php endforeach; ?>
