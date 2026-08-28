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
<body class="d-flex flex-column">

<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <a href="/" class="navbar-brand navbar-brand-autodark fs-2 fw-bold">Motor CMS</a>
        </div>

        <?= $this->fetch('app/_flash') ?>
        <?= $this->section('content') ?>
    </div>
</div>

</body>
</html>
