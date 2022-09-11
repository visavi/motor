<?php
/** @var array<string, int> $tags */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Облако тегов<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Облако тегов</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($tags): ?>
    <div class="section shadow p-3 mb-3">
        <?php foreach ($tags as $tag => $size): ?>
            <a href="/tags/<?= $tag ?>"><span style="font-size:<?= $size ?>pt"><?= $tag ?></span></a>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Тегов еще нет!
    </div>
<?php endif; ?>
