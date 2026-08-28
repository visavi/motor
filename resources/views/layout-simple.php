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

<div class="container-fluid">
    <main>
        <?= $this->fetch('app/_flash') ?>
        <?= $this->section('content') ?>
    </main>
</div>

</body>
</html>
