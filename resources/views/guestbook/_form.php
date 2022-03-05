<?php

use App\Models\File;
use App\Models\Guestbook;
use MotorORM\Collection;

/** @var Guestbook|null $message */
/** @var Collection<File> $files */

$message = $message ?? null;
?>
<div class="p-3 shadow">
    <form method="post" action="/guestbook<?= $message ? '/' . $message->id : '' ?>">
        <input type="hidden" name="_METHOD" value="<?= $message ? 'PUT' : 'POST' ?>">
        <input type="hidden" name="csrf" value="<?= session()->get('csrf') ?>">
        <div class="mb-3">
            <label for="title" class="form-label">Заголовок</label>
            <input type="text" class="form-control<?= hasError('title') ?>" id="title" name="title" value="<?= old('title', $message->title ?? null) ?>" required>
            <div class="invalid-feedback"><?= getError('title') ?></div>
        </div>

        <div class="mb-3">
            <label for="text" class="form-label">Текст</label>
            <textarea class="form-control markItUp<?= hasError('text') ?>" id="text" rows="5" name="text" maxlength="<?= setting('guestbook.text_max_length') ?>" required><?= old('text', $message->text ?? null) ?></textarea>
            <span class="js-textarea-counter"></span>
            <div class="invalid-feedback"><?= getError('text') ?></div>
        </div>

        <?php if (isUser()): ?>
            <?= $this->fetch('app/_upload', compact('message','files')) ?>
        <?php else: ?>
            <?= $this->fetch('app/_captcha') ?>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary"><?= $message ? 'Изменить' : 'Отправить' ?></button>
    </form>
</div>
