<?php
/** @var array $commits */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Последние изменения - Motor CMS<?php $this->stop() ?>
<?php $this->start('description') ?>Список последних изменений Motor CMS<?php $this->stop() ?>

<?php $this->start('header') ?>
    <h2 class="page-title">Последние изменения</h2>
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
    <div class="card card-body mb-3">
        <?php foreach ($commits as $commit): ?>
            <?php
            // Автора нет, если коммит сделан с почты, не привязанной к аккаунту github
            $author = $commit['author'] ?? null;
            $name = $author['login'] ?? $commit['commit']['author']['name'] ?? 'Неизвестный';
            $avatar = $author['avatar_url'] ?? '/assets/images/avatar_default.png';
            ?>
            <div class="post mb-3">
                <div class="post-message fw-bold">
                    <a href="<?= $commit['html_url'] ?>"><?= $commit['commit']['message'] ?></a>
                </div>

                <div class="post-author fw-light">
                    <span class="avatar avatar-sm" style="background-image: url(<?= $avatar ?>)"></span>

                    <span>
                        <?php if ($author): ?>
                            <a href="<?= $author['html_url'] ?>"><?= $name ?></a>
                        <?php else: ?>
                            <?= $name ?>
                        <?php endif; ?>
                    </span>
                    <small class="post-date text-secondary fst-italic"><?= date('d.m.Y H:i', strtotime($commit['commit']['author']['date'])) ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php else: ?>
    <div class="alert alert-warning" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i>
        Не удалось получить последние изменения!
    </div>
<?php endif; ?>
