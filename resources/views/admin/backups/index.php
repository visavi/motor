<?php
/** @var array $files */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Бэкапы<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active"><a href="<?= route('admin') ?>">Админ-панель</a></li>
            <li class="breadcrumb-item active">Бэкапы</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($files): ?>
    <div class="section shadow border p-3 mb-3">
        <?php foreach ($files as $file): ?>
            <div>
                <i class="bi bi-file-zip"></i>
                <?= basename($file) ?> / <?= formatFileSize($file) ?>

                <a href="<?= route('admin-backups-destroy', ['name' => basename($file)]) ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete"><i class="bi bi-x-lg text-body-secondary"></i></a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Бэкапов еще нет!
    </div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= session('csrf') ?>">
    <button type="submit" class="btn btn-primary">Создать бэкап</button>
</form>
