<?php
/** @var array $release */
?>
<?php $this->layout('layout-docs') ?>

<?php $this->start('title') ?>Motor CMS<?php $this->stop() ?>
<?php $this->start('description') ?>Motor CMS - Легкий и быстрый движок для сайта. Не использует базу данных, не требует особых библиотек на сервере<?php $this->stop() ?>

<div class="col-md-8 mx-auto text-center">
    <?php if ($release): ?>
        <a class="d-flex flex-column flex-lg-row justify-content-center align-items-center mb-4 text-dark lh-sm text-decoration-none" href="/docs/versions">
            <strong class="d-sm-inline-block p-2 me-2 mb-2 mb-lg-0 rounded-3 masthead-notice">Новое в <?= $release['tag_name'] ?></strong>
            <span class="text-muted"><?= $release['body'] ?></span>
        </a>
    <?php endif; ?>

    <!--<img src="logo-shadow.png" width="200" height="165" alt="MotorCMS" class="d-block mx-auto mb-3">-->
    <svg width="180px" height="180px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#fff" d="M12 16c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.084 0 2 .916 2 2s-.916 2-2 2-2-.916-2-2 .916-2 2-2z"/><path fill="#fff" d="m2.845 16.136 1 1.73c.531.917 1.809 1.261 2.73.73l.529-.306A8.1 8.1 0 0 0 9 19.402V20c0 1.103.897 2 2 2h2c1.103 0 2-.897 2-2v-.598a8.132 8.132 0 0 0 1.896-1.111l.529.306c.923.53 2.198.188 2.731-.731l.999-1.729a2.001 2.001 0 0 0-.731-2.732l-.505-.292a7.718 7.718 0 0 0 0-2.224l.505-.292a2.002 2.002 0 0 0 .731-2.732l-.999-1.729c-.531-.92-1.808-1.265-2.731-.732l-.529.306A8.1 8.1 0 0 0 15 4.598V4c0-1.103-.897-2-2-2h-2c-1.103 0-2 .897-2 2v.598a8.132 8.132 0 0 0-1.896 1.111l-.529-.306c-.924-.531-2.2-.187-2.731.732l-.999 1.729a2.001 2.001 0 0 0 .731 2.732l.505.292a7.683 7.683 0 0 0 0 2.223l-.505.292a2.003 2.003 0 0 0-.731 2.733zm3.326-2.758A5.703 5.703 0 0 1 6 12c0-.462.058-.926.17-1.378a.999.999 0 0 0-.47-1.108l-1.123-.65.998-1.729 1.145.662a.997.997 0 0 0 1.188-.142 6.071 6.071 0 0 1 2.384-1.399A1 1 0 0 0 11 5.3V4h2v1.3a1 1 0 0 0 .708.956 6.083 6.083 0 0 1 2.384 1.399.999.999 0 0 0 1.188.142l1.144-.661 1 1.729-1.124.649a1 1 0 0 0-.47 1.108c.112.452.17.916.17 1.378 0 .461-.058.925-.171 1.378a1 1 0 0 0 .471 1.108l1.123.649-.998 1.729-1.145-.661a.996.996 0 0 0-1.188.142 6.071 6.071 0 0 1-2.384 1.399A1 1 0 0 0 13 18.7l.002 1.3H11v-1.3a1 1 0 0 0-.708-.956 6.083 6.083 0 0 1-2.384-1.399.992.992 0 0 0-1.188-.141l-1.144.662-1-1.729 1.124-.651a1 1 0 0 0 .471-1.108z"/></svg>

    <h1 class="mb-3 fw-semibold lh-1">Создайте свой первый сайт</h1>
    <p class="lead mb-4">
        Легкий и быстрый движок для сайта. Не использует базу данных, не требует особых библиотек на сервере. Работает абсолютно на всех бесплатных хостингах
    </p>
    <div class="d-flex flex-column flex-lg-row align-items-md-stretch justify-content-md-center gap-3 mb-4">
        <div class="d-inline-block v-align-middle fs-5">
            <div class="highlight"><pre tabindex="0" class="chroma"><span class="line"><span class="cl">composer create-project visavi/motor .</span></span></pre></div>
        </div>
        <a href="/docs" class="btn btn-lg bd-btn-lg btn-bd-primary d-flex align-items-center justify-content-center fw-semibold">
            <svg class="bi me-2" aria-hidden="true"><use xlink:href="#book-half"></use></svg>
            Документация
        </a>
    </div>
    <p class="text-muted mb-0">
        <?php if ($release): ?>
            Версия <strong><?= $release['tag_name'] ?></strong>
            <span class="px-1">&middot;</span>
        <?php endif; ?>
        <a href="/docs/versions" class="link-secondary text-nowrap">Последние версии</a>
        <span class="px-1">&middot;
        <a href="/docs/commits" class="link-secondary text-nowrap">История изменений</a>
    </p>
</div>
