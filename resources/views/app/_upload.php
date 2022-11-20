<?php

use App\Models\File;
use App\Models\Story;
use MotorORM\Collection;

/** @var Story|null $story */
/** @var Collection<File> $files */

$paste = true;
$click = empty($paste) ? null : 'return pasteImage(this);';
$pointer = empty($paste)  ? null : 'cursor-pointer';
?>
<div class="js-files mb-3">
    <?php if ($files->isNotEmpty()): ?>
        <?php foreach ($files as $file): ?>
            <span class="js-file">
                <?php if ($file->isImage()): ?>
                    <span onclick="<?= $click ?>" class="<?= $pointer ?>"><img src="<?= $file->path ?>" width="100" alt="<?= $file->name ?>" class="img-fluid"></span>
                <?php else: ?>
                    <span class="upload-file">
                        <a href="<?= $file->path ?>"><?= $file->name ?></a>
                        <?= formatSize($file->size) ?>
                    </span>
                <?php endif; ?>

                <a href="#" onclick="return deleteFile(this);" data-id="<?= $file->id ?>" data-type="image" data-csrf="<?= session('csrf') ?>" class="js-file-delete"><i class="bi bi-x-lg"></i></a>
            </span>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="js-file-template d-none">
    <span class="js-file">
        <span class="upload-file">
            <a href="#" class="js-file-link"></a> <span class="js-file-size"></span>
       </span>
        <a href="#" onclick="return deleteFile(this);" data-type="file" data-csrf="<?= session('csrf') ?>" class="js-file-delete"><i class="bi bi-x-lg"></i></a>
    </span>
</div>

<div class="js-image-template d-none">
    <span class="js-file">
        <span onclick="<?= $click ?>" class="<?= $pointer ?>"><img src="#" width="100" alt="" class="img-fluid"></span>
        <a href="#" onclick="return deleteFile(this);" data-type="image" data-csrf="<?= session('csrf') ?>" class="js-file-delete"><i class="bi bi-x-lg"></i></a>
    </span>
</div>

<div class="mb-3">
    <label class="btn btn-sm btn-secondary mb-1" for="file">
        <input id="file" type="file" name="file" onchange="return submitFile(this);" data-id="<?= $story->id ?? 0 ?>" data-csrf="<?= session()->get('csrf') ?>" hidden>
        Прикрепить файл&hellip;
    </label>
</div>
