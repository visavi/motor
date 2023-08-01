<?php

use App\Models\Guestbook;

/** @var Guestbook|null $message */

$message ??= null;
?>
<div class="section shadow border post-form p-3 mt-3">
    <form method="post" action="/guestbook<?= $message ? '/' . $message->id : '' ?>">
        <input type="hidden" name="_METHOD" value="<?= $message ? 'PUT' : 'POST' ?>">
        <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

        <?php if (! isUser()): ?>
            <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input type="text" class="form-control<?= hasError('name') ?>" id="name" name="name" value="<?= old('name', $message->name ?? null) ?>" required>
                <div class="invalid-feedback"><?= getError('name') ?></div>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="text" class="form-label">Сообщение</label>
            <textarea class="form-control markItUp<?= hasError('text') ?>" id="text" rows="5" name="text" maxlength="<?= setting('guestbook.text_max_length') ?>" required><?= old('text', $message->text ?? null) ?></textarea>
            <span class="js-textarea-counter"></span>
            <div class="invalid-feedback"><?= getError('text') ?></div>
        </div>

        <?php if ($message && isAdmin()): ?>
        <div class="mb-3">
            <div class="form-check">
                <input type="hidden" value="0" name="active">
                <input type="checkbox" class="form-check-input" value="1" name="active" id="active"<?= old('active', $message->active ?? true) ? ' checked' : '' ?>>
                <label for="active" class="form-check-label">Опубликовать</label>
            </div>
        </div>
        <?php endif; ?>

        <?php if (! isUser()): ?>
            <?= $this->fetch('app/_captcha') ?>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary"><?= $message ? 'Изменить' : 'Отправить' ?></button>
    </form>
</div>
