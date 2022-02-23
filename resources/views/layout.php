<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $this->section('description', 'Motor CMS') ?>">
    <meta name="generator" content="Motor CMS">
    <title><?= $this->section('title', 'Motor CMS') ?></title>

    <link href="<?= $this->asset('/assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= $this->asset('/assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= $this->asset('/assets/css/markitup.css') ?>" rel="stylesheet">
    <link href="<?= $this->asset('/assets/css/prettify.css') ?>" rel="stylesheet">
    <link href="<?= $this->asset('/assets/css/main.css') ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" rel="stylesheet">
    <link href="/favicon.ico" rel="icon" type="image/x-icon" >
    <meta name="theme-color" content="#7952b3">
</head>
<body>

<div class="col-lg-8 mx-auto p-3 py-md-4">
    <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
        <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
            <svg width="40px" height="32px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 16c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.084 0 2 .916 2 2s-.916 2-2 2-2-.916-2-2 .916-2 2-2z"/><path d="m2.845 16.136 1 1.73c.531.917 1.809 1.261 2.73.73l.529-.306A8.1 8.1 0 0 0 9 19.402V20c0 1.103.897 2 2 2h2c1.103 0 2-.897 2-2v-.598a8.132 8.132 0 0 0 1.896-1.111l.529.306c.923.53 2.198.188 2.731-.731l.999-1.729a2.001 2.001 0 0 0-.731-2.732l-.505-.292a7.718 7.718 0 0 0 0-2.224l.505-.292a2.002 2.002 0 0 0 .731-2.732l-.999-1.729c-.531-.92-1.808-1.265-2.731-.732l-.529.306A8.1 8.1 0 0 0 15 4.598V4c0-1.103-.897-2-2-2h-2c-1.103 0-2 .897-2 2v.598a8.132 8.132 0 0 0-1.896 1.111l-.529-.306c-.924-.531-2.2-.187-2.731.732l-.999 1.729a2.001 2.001 0 0 0 .731 2.732l.505.292a7.683 7.683 0 0 0 0 2.223l-.505.292a2.003 2.003 0 0 0-.731 2.733zm3.326-2.758A5.703 5.703 0 0 1 6 12c0-.462.058-.926.17-1.378a.999.999 0 0 0-.47-1.108l-1.123-.65.998-1.729 1.145.662a.997.997 0 0 0 1.188-.142 6.071 6.071 0 0 1 2.384-1.399A1 1 0 0 0 11 5.3V4h2v1.3a1 1 0 0 0 .708.956 6.083 6.083 0 0 1 2.384 1.399.999.999 0 0 0 1.188.142l1.144-.661 1 1.729-1.124.649a1 1 0 0 0-.47 1.108c.112.452.17.916.17 1.378 0 .461-.058.925-.171 1.378a1 1 0 0 0 .471 1.108l1.123.649-.998 1.729-1.145-.661a.996.996 0 0 0-1.188.142 6.071 6.071 0 0 1-2.384 1.399A1 1 0 0 0 13 18.7l.002 1.3H11v-1.3a1 1 0 0 0-.708-.956 6.083 6.083 0 0 1-2.384-1.399.992.992 0 0 0-1.188-.141l-1.144.662-1-1.729 1.124-.651a1 1 0 0 0 .471-1.108z"/></svg>
            <span class="fs-4">Motor CMS</span>
        </a>

        <nav class="d-inline-flex mt-2 ms-auto">
            <?php if (isUser()): ?>
                <a class="me-3 py-2 text-dark text-decoration-none" href="/users/<?= getUser('login') ?>"><?= getUser('login') ?></a>
                <a class="me-3 py-2 text-dark text-decoration-none" href="#"
                   onclick="return (confirm('Подтвердите выход!')) ? $(this).find('form').submit() : false;">
                    Выйти
                    <form action="/logout" method="post" style="display:none">
                        <input type="hidden" name="csrf" value="<?= session()->get('csrf') ?>">
                    </form>
                </a>

            <?php else: ?>
                <a class="me-3 py-2 text-dark text-decoration-none" href="/login">Войти</a>
                <a class="me-3 py-2 text-dark text-decoration-none" href="/register">Регистрация</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?= $this->fetch('app/_flash') ?>
        <?= $this->section('breadcrumb') ?>
        <?= $this->section('content') ?>
    </main>

    <footer class="pt-5 my-5 text-muted border-top">
        Motor CMS &middot; &copy; <?= date('Y') ?>
    </footer>
</div>

<script src="<?= $this->asset('/assets/js/jquery-3.6.0.min.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/jquery.caret.min.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/markitup.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/markitup-set.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/prettify.js') ?>"></script>
<script src="<?= $this->asset('/assets/js/main.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
</body>
</html>
