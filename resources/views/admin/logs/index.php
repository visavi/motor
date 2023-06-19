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
    <div class="section shadow border p-3 mb-3">
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
            <div class="section shadow border p-3 mb-3">
                <i class="bi bi-bug"></i> <b><?= $data['level'] ?></b>
                <small class="post-date text-body-secondary fst-italic ms-1">
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
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill text-danger"></i>
        Логов еще нет!
    </div>
<?php endif; ?>


<?php $this->push('scripts') ?>
    <script>
        showContext = function (el) {
            $(el).next('.js-context').slideToggle();

            return false;
        };

        selectLog = function (el) {
            window.location = '?log=' + $(el).val();

            return false;
        };
    </script>
<?php $this->end() ?>
