<?php

use App\Models\Story;
use MotorORM\CollectionPaginate;

/** @var string $search */
/** @var CollectionPaginate|Story[] $stories */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?><?= $search ?> (Стр. <?= $stories->currentPage() ?>)<?php $this->stop() ?>
<?php $this->start('description') ?><?= $search ?> (Стр. <?= $stories->currentPage() ?>)<?php $this->stop() ?>

<?php $this->start('header') ?>
    <h1>Поиск по тексту: <?= $search ?></h1>
<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item"><a href="/stories">Статьи</a></li>
        <li class="breadcrumb-item active">Поиск по тексту</li>
    </ol>
</nav>
<?php $this->stop() ?>

<div class="mb-3">
    <form class="row row-cols-sm-auto g-3" action="/search" method="get"  role="search">
        <div class="col-sm-10">
            <input type="text" name="search" class="form-control" placeholder="Поиск..." aria-label="Search" value="<?= $search ?>" required>
        </div>

        <div class="col-sm-2 d-sm-grid">
            <button type="submit" class="btn btn-primary">Поиск</button>
        </div>
    </form>
</div>

<?= $this->fetch('stories/_list', compact('stories')) ?>
