<?php
/** @var array<string, int> $tags */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Облако тегов<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('stories') ?>">Статьи</a></li>
            <li class="breadcrumb-item active">Облако тегов</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($tags): ?>
    <div class="card card-body mb-3">
        <div class="d-flex flex-wrap align-items-baseline gap-2">
            <?php foreach ($tags as $tag => $size): ?>
                <a href="/tags/<?= urlencode($tag) ?>" style="font-size: <?= $size ?>pt"><?= escape($tag) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <?= $this->fetch('app/_empty', [
        'title'    => 'Тегов ещё нет',
        'subtitle' => 'Теги появятся, когда их проставят у статей',
        'icon'     => 'bi-tags',
    ]) ?>
<?php endif; ?>
