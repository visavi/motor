<?php
/** @var array $releases */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Последние версии - Motor CMS<?php $this->stop() ?>
<?php $this->start('description') ?>Список последних версий Motor CMS<?php $this->stop() ?>

<?php $this->start('header') ?>
    <h1>Последние версии</h1>
<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item active">Последние версии</li>
    </ol>
</nav>
<?php $this->stop() ?>

<?php if ($releases): ?>
    <div class="section shadow border p-3 mb-3">
        <?php foreach ($releases as $release): ?>
            <div class="post mb-3">
                <div class="post-message fw-bold">
                    <a href="<?= $release['html_url'] ?>"><?= $release['name'] ?></a>
                </div>

                <div class="post-message">
                    <?= $release['body'] ?>
                </div>

                <div class="post-author fw-light">
                    <span class="avatar-micro">
                        <img class="avatar-default rounded-circle" src="<?= $release['author']['avatar_url']?>" alt="Аватар">
                    </span>

                    <span><a href="<?= $release['author']['html_url'] ?>"><?= $release['author']['login'] ?></a></span>
                    <small class="post-date text-body-secondary fst-italic"><?= date('d.m.Y H:i', strtotime($release['created_at'])) ?></small>
                </div>

                <div>
                    Скачать: <a href="<?= $release['zipball_url'] ?>"><?= $release['tag_name'] ?></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Не удалось получить последние версии!
    </div>
<?php endif; ?>
