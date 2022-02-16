<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Ошибка 403<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Ошибка 403</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<div class="col-12 text-center">
    <h1>Ошибка 403!</h1>

    <?php if (isset($message)): ?>
        <div class="lead"><?= $message ?></div>
    <?php endif; ?>
</div>
