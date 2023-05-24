<?php
/** @var string $name */
/** @var array $files */
/** @var int $countFiles */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?><?= $name ?><?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('admin') ?>">Админ-панель</a></li>
            <li class="breadcrumb-item"><a href="<?= route('admin-backups') ?>">Бэкапы</a></li>
            <li class="breadcrumb-item active"><?= $name ?></li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($files): ?>
    <div class="section shadow border p-3 mb-3">
        <?php foreach ($files as $file): ?>
            <div>
                <i class="bi bi-filetype-csv"></i>
                <?= $file['name'] ?> / <?= formatSize($file['size']) ?>
            </div>
        <?php endforeach; ?>

        <div class="mt-3">Всего таблиц: <?= $countFiles ?></div>
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Таблиц нет!
    </div>
<?php endif; ?>
