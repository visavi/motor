<?php $this->layout('layout') ?>

<?php $this->push('title') ?>Гостевая книга (Стр. <?= $paginator->page ?>)<?php $this->stop() ?>
<?php $this->start('description') ?>Гостевая книга (Стр. <?= $paginator->page ?>)<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Гостевая книга</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($messages): ?>
    <?php foreach ($messages as $message): ?>
        <div class="shadow p-3 mb-3">
            <?php if (isAdmin()): ?>
                <div class="float-end">
                    <a href="/guestbook/<?= $message->id ?>/edit"><i class="bi bi-pencil"></i></a>
                    <a href="guestbook/<?= $message->id ?>/delete" onclick="return confirm('Подтвердите удаление!')"><i class="bi bi-x-lg"></i></a>
                </div>
            <?php endif; ?>

            <h5><?= $this->e($message->title) ?></h5>

            <?php if ($message->image): ?>
                <div class="media-file">
                    <img src="<?= $message->image ?>" alt="" class="w-100">
                </div>
            <?php endif; ?>

            <div class="message">
                <?= bbCode($message->text) ?>
            </div>

            <span class="fw-bold"><?= $this->e($message->login) ?></span>
            <small class="text-muted fst-italic"><?= date('d.m.Y H:i', $message->created_at) ?></small>
        </div>
    <?php endforeach; ?>

    <?= $paginator->links() ?>
<?php else: ?>
    <div class="alert alert-danger">Сообщений еще нет!</div>
<?php endif; ?>

<?php if (isUser()): ?>
    <?= $this->fetch('guestbook/_form') ?>
<?php else: ?>
    Авторизуйтесь для добавления сообщений
<?php endif; ?>
