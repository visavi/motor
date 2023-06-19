<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Админ-панель<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Админ-панель</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<div class="container">
    <div class="row">
        <div class="col">
            <a href="<?= route('admin-settings') ?>"><i class="bi bi-tools" style="font-size: 4rem;"></i> Настройки</a>
        </div>
        <div class="col">
            <a href="<?= route('admin-backups') ?>"><i class="bi bi-database" style="font-size: 4rem;"></i> Бэкапы</a>
        </div>
        <div class="col">
            <a href="<?= route('admin-logs') ?>"><i class="bi bi-list-columns" style="font-size: 4rem;"></i> Логи</a>
        </div>
    </div>
</div>
