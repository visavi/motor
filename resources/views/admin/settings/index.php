<?php
/** @var array $settings */
/** @var string $action */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Настройки сайта<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Настройки сайта</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 section shadow p-3">
            <div class="nav flex-column nav-pills">
                <a class="nav-link" href="/admin/settings?action=main" id="main">Основные</a>
                <a class="nav-link" href="/admin/settings?action=story" id="story">Статьи</a>
                <a class="nav-link" href="/admin/settings?action=comment" id="comment">Комментарии</a>
                <a class="nav-link" href="/admin/settings?action=guestbook" id="guestbook">Гостевая</a>
                <a class="nav-link" href="/admin/settings?action=file" id="file">Файлы</a>
                <a class="nav-link" href="/admin/settings?action=image" id="image">Изображения</a>
                <a class="nav-link" href="/admin/settings?action=captcha" id="captcha">Captcha</a>
                <a class="nav-link" href="/admin/settings?action=user" id="user">Пользователи</a>
            </div>
        </div>
        <div class="col-md-8 section shadow p-3">
            <?= $this->fetch('admin/settings/_' . $action, ['settings' => $settings, 'template' => $this]) ?>
        </div>
    </div>
</div>

<?php $this->push('scripts') ?>
<script>
    $(function () {
        $('#<?= $action ?>').addClass('active');
    })
</script>
<?php $this->end() ?>
