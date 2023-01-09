<!doctype html>
<html lang="ru" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $this->section('description', 'Motor CMS') ?>">
    <meta name="generator" content="Motor CMS">
    <title><?= $this->section('title', 'Motor CMS') ?></title>

    <link href="<?= $this->asset('/assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= $this->asset('/assets/css/variables.css') ?>" rel="stylesheet">
    <link href="<?= $this->asset('/assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= $this->asset('/assets/css/markitup.css') ?>" rel="stylesheet">
    <link href="<?= $this->asset('/assets/css/prettify.css') ?>" rel="stylesheet">
    <link href="<?= $this->asset('/assets/css/toastr.min.css') ?>" rel="stylesheet">
    <link href="<?= $this->asset('/assets/css/fancybox.css') ?>" rel="stylesheet">
    <link href="<?= $this->asset('/assets/css/bootstrap-tags.css') ?>" rel="stylesheet">
    <link href="<?= $this->asset('/assets/css/main.css') ?>" rel="stylesheet">
    <?= $this->section('styles') ?>
    <link href="/favicon.ico" rel="icon" type="image/x-icon" >
    <meta name="theme-color" content="#7952b3">
</head>
<body class="body">

<?= $this->fetch('header') ?>

<div class="col-lg-10 mx-auto p-3 py-md-4">
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
            <div class="col-md-8">
                <?= $this->section('content') ?>
            </div>
            <div class="col-md-4">
                <?= $this->fetch('sidebar') ?>
            </div>
        </div>
    </main>
</div>

<?= $this->fetch('footer') ?>

<script src="<?= $this->asset('/assets/js/jquery-3.6.3.min.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/theme-toggler.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/jquery.caret.min.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/markitup.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/markitup-set.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/prettify.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/toastr.min.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/fancybox.umd.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/main.js') ?>"></script>

<script type="module">
    import Tags from "<?= $this->asset('/assets/js/bootstrap-tags.min.js') ?>"
    Tags.init(".input-tag", {
        allowNew: true,
        server: "/tag",
        liveServer: true,
        clearEnd: true,
        allowClear: true,
        suggestionsThreshold: 2,
        max: <?= setting('story.tags_max') ?>,
        separator: [','],
        addOnBlur: true,
    });
</script>
<?= $this->section('scripts') ?>

</body>
</html>
