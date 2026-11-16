<?php

use App\Models\Migration;
use MotorORM\Collection;

/** @var Collection<Migration> $applied */
/** @var string[] $pending */
/** @var array|null $failure */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Миграции<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('admin') ?>">Админ-панель</a></li>
            <li class="breadcrumb-item active">Миграции</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($failure): ?>
    <div class="alert alert-danger">
        <h4 class="alert-title">Обновление таблиц не выполнено</h4>
        <div class="mb-1">
            Миграция: <code><?= escape($failure['migration'] ?? '—') ?></code>
        </div>
        <div class="mb-1">Ошибка: <?= escape($failure['message'] ?? '') ?></div>
        <div class="mb-1">Бэкап перед обновлением: <?= escape($failure['backup'] ?? 'не создавался') ?></div>
        <div class="text-secondary small">
            <?= escape($failure['time'] ?? '') ?>. Подробности — в журнале. Пока ошибка не устранена, сайт отвечает
            посетителям «Сайт обновляется».
        </div>
    </div>
<?php endif; ?>

<?php if ($pending): ?>
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Невыполненные миграции</h3>
            <div class="card-actions">
                <span class="badge bg-yellow-lt"><?= count($pending) ?></span>
            </div>
        </div>
        <div class="list-group list-group-flush">
            <?php foreach ($pending as $name): ?>
                <div class="list-group-item"><code><?= escape($name) ?></code></div>
            <?php endforeach; ?>
        </div>
        <div class="card-footer">
            <form action="<?= route('admin-migrations-run') ?>" method="post">
                <input type="hidden" name="csrf" value="<?= session('csrf') ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-play-fill me-1"></i> <?= $failure ? 'Повторить' : 'Выполнить' ?>
                </button>
                <span class="text-secondary small ms-2">Перед выполнением будет создан бэкап таблиц</span>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if ($applied->isNotEmpty()): ?>
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Выполненные миграции</h3>
            <div class="card-actions">
                <span class="badge bg-secondary-lt"><?= $applied->count() ?></span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Миграция</th>
                        <th class="w-1">Партия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applied as $migration): ?>
                        <tr>
                            <td><code><?= escape($migration->name) ?></code></td>
                            <td class="text-secondary text-center"><?= $migration->batch ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <?= $this->fetch('app/_empty', [
        'title'    => 'Миграций ещё нет',
        'subtitle' => 'Таблицы создаются автоматически при первом открытии сайта',
        'icon'     => 'bi-database',
    ]) ?>
<?php endif; ?>
