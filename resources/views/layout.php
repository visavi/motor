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
    <link href="/favicon.ico" rel="icon" type="image/x-icon">
    <meta name="theme-color" content="#066fd1">
</head>
<body>

<div class="page">
    <?= $this->fetch('header') ?>

    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="container-xl">
                <?= $this->section('breadcrumb') ?>

                <?php if ($this->section('header')): ?>
                    <?= $this->section('header') ?>
                <?php else: ?>
                    <h2 class="page-title"><?= $this->section('title') ?></h2>
                <?php endif ?>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">
                <?= $this->fetch('app/_flash') ?>

                <div class="row row-cards">
                    <div class="col-lg-8">
                        <?= $this->section('content') ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $this->fetch('sidebar') ?>
                    </div>
                </div>
            </div>
        </div>

        <?= $this->fetch('footer') ?>
    </div>
</div>

<?= $this->section('scripts') ?>

</body>
</html>
