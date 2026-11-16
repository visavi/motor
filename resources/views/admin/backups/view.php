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
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Таблицы в архиве</h3>
            <div class="card-actions">
                <span class="badge bg-secondary-lt"><?= $countFiles ?></span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Таблица</th>
                        <th class="w-1">Размер</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): ?>
                        <tr>
                            <td>
                                <i class="bi bi-filetype-csv text-secondary me-1"></i>
                                <?= escape($file['name']) ?>
                            </td>
                            <td class="text-secondary text-nowrap"><?= formatSize($file['size']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <?= $this->fetch('app/_empty', [
        'title'    => 'Таблиц нет',
        'subtitle' => 'В этом архиве не оказалось ни одной таблицы',
        'icon'     => 'bi-table',
    ]) ?>
<?php endif; ?>
