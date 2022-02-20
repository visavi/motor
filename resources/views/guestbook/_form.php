<?php
use App\Models\Guestbook;

/** @var Guestbook|null $message */
?>
<div class="p-3 shadow">
    <form method="post" action="/guestbook/<?= isset($message) ? $message->id . '/edit' : 'create' ?>"  enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Заголовок</label>
            <input type="text" class="form-control<?= hasError('title') ?>" id="title" name="title" value="<?= old('title', $message->title ?? null) ?>" required>
            <div class="invalid-feedback"><?= getError('title') ?></div>
        </div>

        <div class="mb-3">
            <label for="text" class="form-label">Текст</label>
            <textarea class="form-control markItUp<?= hasError('text') ?>" id="text" rows="5" name="text" maxlength="5000" required><?= old('text', $message->text ?? null) ?></textarea>
            <span class="js-textarea-counter"></span>
            <div class="invalid-feedback"><?= getError('text') ?></div>
        </div>

        <div class="mb-3">
            <label for="image" class="btn btn-sm btn-secondary form-label<?= hasError('image') ?>">
                <input id="image" type="file" name="image" onchange="$('#upload-file-info').html(this.files[0].name);" hidden>
                Прикрепить фото&hellip;
            </label>
            <div class="invalid-feedback"><?= getError('image') ?></div>
            <span class="badge bg-info" id="upload-file-info"></span>
        </div>

        <?php if (! isUser()): ?>
            <?= $this->fetch('app/_captcha') ?>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary"><?= isset($message) ? 'Изменить' : 'Отправить' ?></button>
    </form>
</div>
