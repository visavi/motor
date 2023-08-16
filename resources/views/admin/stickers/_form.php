<?php

use App\Models\Sticker;

/** @var Sticker|null $sticker */

$sticker ??= null;
?>
<div class="section shadow border post-form p-3 mt-3">
    <form method="post" action="<?= route($sticker ? 'admin-sticker-update' : 'admin-sticker-store', ['id' => $sticker->id]) ?>" enctype="multipart/form-data">
        <input type="hidden" name="_METHOD" value="<?= $sticker ? 'PUT' : 'POST' ?>">
        <input type="hidden" name="csrf" value="<?= session('csrf') ?>">

        <?php if ($sticker): ?>
            <img src="<?= $sticker->path ?>" alt="">
        <?php endif; ?>

        <div class="mb-3">
            <label for="code" class="form-label">Код стикера:</label>
            <input type="text" class="form-control<?= hasError('code') ?>" id="code" name="code" value="<?= old('code', $sticker->code) ?>" required>
            <div class="invalid-feedback"><?= getError('code') ?></div>
        </div>

        <?php if (! $sticker): ?>
            <div class="mb-3">
                <label for="file" class="btn btn-sm btn-secondary form-label<?= hasError('file') ?>">
                    <input id="file" type="file" name="file" onchange="$('#upload-file-info').html(this.files[0].name);" hidden>
                    Прикрепить стикер&hellip;
                </label>
                <div class="invalid-feedback"><?= getError('file') ?></div>
                <span class="badge bg-info" id="upload-file-info"></span>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary"><?= $sticker ? 'Изменить' : 'Создать' ?></button>
    </form>
</div>
