<?php
/** @var array $logs */
/** @var array $reader */
/** @var string $currentLog */
?>
<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Логи<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= route('admin') ?>">Админ-панель</a></li>
            <li class="breadcrumb-item active">Логи</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<?php if ($logs): ?>
    <div class="card card-body mb-3">
        <div class="mb-3">
            <select class="form-select" id="log" onchange="return selectLog(this);">
                <?php foreach ($logs as $log): ?>
                    <option value="<?= basename($log) ?>"<?= $currentLog === basename($log) ? ' selected' : '' ?>>
                        <?= basename($log) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php foreach ($reader as $data): ?>
            <div class="card card-body mb-3">
                <i class="bi bi-bug"></i> <b><?= $data['level'] ?></b>
                <small class="post-date text-secondary fst-italic ms-1">
                    <?= $data['date'] ?>
                </small>
                <div>
                    Message: <?= $data['message'] ?><br>

                    <?php if (isset($data['context']['method'])): ?>
                        Method: <?= $data['context']['method']?><br>
                    <?php endif; ?>

                    <?php if (isset($data['context']['url'])): ?>
                        URL: <?= $data['context']['url'] ?><br>
                    <?php endif; ?>
                </div>

                <div>
                    <a href="#" onclick="return showContext(this);">
                        <i class="bi bi-arrow-down-short"></i>
                        Полная информация
                    </a>

                    <div class="post-message js-context" style="display: none;">
                        <?= print_r($data['context'], true) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <?= $this->fetch('app/_empty', [
        'title'    => 'Логов ещё нет',
        'subtitle' => 'Здесь появятся записи из storage/logs',
        'icon'     => 'bi-journal-text',
    ]) ?>
<?php endif; ?>


<?php $this->push('scripts') ?>
    <script>
        showContext = function (el) {
            const context = el.nextElementSibling;
            context.hidden = ! context.hidden;

            return false;
        };

        selectLog = function (el) {
            window.location = '?log=' + el.value;

            return false;
        };
    </script>
<?php $this->end() ?>
