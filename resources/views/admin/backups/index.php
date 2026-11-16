<?php
/** @var array $files */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Бэкапы<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('admin') ?>">Админ-панель</a></li>
            <li class="breadcrumb-item active">Бэкапы</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($files): ?>
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Архивы</h3>
            <div class="card-actions">
                <span class="badge bg-secondary-lt"><?= count($files) ?></span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Архив</th>
                        <th class="w-1">Размер</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): ?>
                        <tr>
                            <td>
                                <i class="bi bi-file-zip text-secondary me-1"></i>
                                <a href="<?= route('admin-backups-view', ['name' => basename($file)]) ?>"><?= basename($file) ?></a>
                            </td>
                            <td class="text-secondary text-nowrap"><?= formatFileSize($file) ?></td>
                            <td>
                                <a href="<?= route('admin-backups-destroy', ['name' => basename($file)]) ?>" class="btn btn-icon btn-sm btn-ghost-danger" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-method="delete" title="Удалить" data-bs-toggle="tooltip">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <?= $this->fetch('app/_empty', [
        'title'    => 'Бэкапов ещё нет',
        'subtitle' => 'Создайте первый командой php motor backup',
        'icon'     => 'bi-archive',
    ]) ?>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= session('csrf') ?>">
    <button type="submit" class="btn btn-primary">Создать бэкап</button>
</form>
