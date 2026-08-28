<!doctype html>
<html lang="ru" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $this->section('description', 'Motor CMS') ?>">
    <meta name="generator" content="Motor CMS">
    <title><?= $this->section('title', 'Motor CMS') ?></title>

    <?= vite() ?>
    <?= $this->section('styles') ?>
    <link href="/favicon.ico" rel="icon" type="image/x-icon" >
    <meta name="theme-color" content="#7952b3">
</head>
<body class="body">

<?= $this->fetch('header') ?>

<div class="container-xxl col-lg-10 mx-auto p-3 py-md-4">
    <main>
        <?= $this->fetch('app/_flash') ?>

        <div class="app-title">
            <?php if ($this->section('header')): ?>
                <?= $this->section('header') ?>
            <?php else: ?>
                <h1><?= $this->section('title') ?></h1>
            <?php endif ?>

            <?= $this->section('breadcrumb') ?>
        </div>

        <div class="row">
            <div class="col-md-3">
                <?= $this->fetch('sidebar-docs') ?>
            </div>
            <div class="col-md-9">
                <?= $this->section('content') ?>
            </div>
        </div>
    </main>
</div>

<?= $this->fetch('footer') ?>

<?= $this->section('scripts') ?>

</body>
</html>
