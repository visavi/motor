<?php

use App\Models\File;
use App\Models\Story;
use League\Plates\Template\Template;
use MotorORM\Collection;

/** @var Template $template */
/** @var Story|null $story */
/** @var Collection<File> $files */

$story ??= null;
?>
<div class="section shadow border p-3 cut">
    <form method="post" action="<?= route($story ? 'story-update' : 'story-store', ['id' => $story->id ?? null]) ?>">
        <input type="hidden" name="_METHOD" value="<?= $story ? 'PUT' : 'POST' ?>">
        <input type="hidden" name="csrf" value="<?= session('csrf') ?>">
        <div class="mb-3">
            <label for="title" class="form-label">Заголовок</label>
            <input type="text" class="form-control<?= hasError('title') ?>" id="title" name="title" value="<?= old('title', $story->title ?? null) ?>" required>
            <div class="invalid-feedback"><?= getError('title') ?></div>
        </div>

        <div class="mb-3">
            <label for="text" class="form-label">Текст</label>
            <textarea class="form-control markItUp<?= hasError('text') ?>" id="text" rows="5" name="text" maxlength="<?= setting('story.text_max_length') ?>" required><?= old('text', $story->text ?? null) ?></textarea>
            <span class="js-textarea-counter"></span>
            <div class="invalid-feedback"><?= getError('text') ?></div>
        </div>

        <?= $this->fetch('app/_upload', compact('story', 'files')) ?>

        <div class="mb-3">
            <label for="tags" class="form-label">Теги</label>

            <?php $tags = old('tags', $story ? $story->tags->pluck('tag')->all() : []); ?>
            <select class="form-select input-tag<?= hasError('tags') ?>" id="tags" name="tags[]" multiple required>
                <option disabled value="">Теги...</option>
                <?php foreach ($tags as $key => $tag): ?>
                <option value="<?= $tag ?>" selected><?= $tag ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback"><?= getError('tags') ?></div>
        </div>

        <?php if (isAdmin()): ?>
            <div class="mb-3">
                <div class="form-check">
                    <input type="hidden" value="0" name="active">
                    <input type="checkbox" class="form-check-input" value="1" name="active" id="active"<?= old('active', $story->active ?? true) ? ' checked' : '' ?>>
                    <label for="active" class="form-check-label">Опубликовать статью</label>
                </div>

                <div class="form-check">
                    <input type="hidden" value="0" name="locked">
                    <input type="checkbox" class="form-check-input" value="1" name="locked" id="locked"<?= old('locked', $story->locked ?? null) ? ' checked' : '' ?>>
                    <label for="locked" class="form-check-label">Закрепить статью</label>
                </div>

                <?php $delay = old('delay') || $story?->created_at > time(); ?>
                <?php $checked = $delay ? ' checked' : ''; ?>
                <?php $display = $delay ? '' : ' style="display: none"'; ?>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" value="1" name="delay" id="delay"<?= $checked ?> onchange="return showDelayForm(this);">
                    <label for="delay" class="form-check-label">Отложенная публикация</label>
                </div>

                <div class="col-sm-6 col-md-4 my-3 js-created"<?= $display ?>>
                    <label for="created" class="form-label">Дата публикации</label>
                    <input class="form-control<?= hasError('created') ?>" type="datetime-local" name="created" id="created" value="<?= old('created', date('Y-m-d\TH:i', $story?->created_at)) ?>">
                    <div class="invalid-feedback"><?= getError('created') ?></div>
                </div>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary"><?= $story ? 'Изменить' : 'Отправить' ?></button>
    </form>
</div>

<?php $template->push('scripts') ?>
    <script>
        showDelayForm = function (el) {
            if($(el).is(":checked")) {
                $('.js-created').show(300);
            } else {
                $('.js-created').hide(200);
            }

            return false;
        };
    </script>
<?php $template->end() ?>
