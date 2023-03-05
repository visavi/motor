<?php
/** @var array $commits */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Последние изменения - Motor CMS<?php $this->stop() ?>
<?php $this->start('description') ?>Список последних изменений Motor CMS<?php $this->stop() ?>

<?php $this->start('header') ?>
    <h1>Последние изменения</h1>
<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item active">Последние изменения</li>
    </ol>
</nav>
<?php $this->stop() ?>

<?php if ($commits): ?>
    <div class="section shadow border p-3 mb-3">
        <?php foreach ($commits as $commit): ?>
            <div class="post mb-3">
                <div class="post-message fw-bold">
                    <a href="<?= $commit['html_url'] ?>"><?= $commit['commit']['message'] ?></a>
                </div>

                <div class="post-author fw-light">
                    <span class="avatar-micro">
                        <img class="avatar-default rounded-circle" src="<?= $commit['author']['avatar_url']?>" alt="Аватар">
                    </span>

                    <span><a href="<?= $commit['author']['html_url'] ?>"><?= $commit['author']['login'] ?></a></span>
                    <small class="post-date text-body-secondary fst-italic"><?= date('d.m.Y H:i', strtotime($commit['commit']['author']['date'])) ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Не удалось получить последние изменения!
    </div>
<?php endif; ?>
