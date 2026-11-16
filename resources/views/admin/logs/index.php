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
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Журнал</h3>
            <div class="card-actions">
                <select class="form-select form-select-sm" id="log" onchange="return selectLog(this);">
                    <?php foreach ($logs as $log): ?>
                        <option value="<?= basename($log) ?>"<?= $currentLog === basename($log) ? ' selected' : '' ?>>
                            <?= basename($log) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th class="w-1">Уровень</th>
                        <th>Сообщение</th>
                        <th class="w-1">Когда</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reader as $data): ?>
                        <tr>
                            <td>
                                <span class="badge <?= in_array($data['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'], true) ? 'bg-red-lt' : 'bg-yellow-lt' ?>">
                                    <?= escape($data['level']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="text-break"><?= escape($data['message']) ?></div>

                                <?php if (isset($data['context']['method'], $data['context']['url'])): ?>
                                    <div class="text-secondary small mt-1">
                                        <?= escape($data['context']['method']) ?> <?= escape($data['context']['url']) ?>
                                    </div>
                                <?php endif; ?>

                                <a href="#" class="small" onclick="return showContext(this);">
                                    <i class="bi bi-arrow-down-short"></i>
                                    Полная информация
                                </a>

                                <pre class="post-message js-context mt-2 mb-0 small" hidden><?= escape(print_r($data['context'], true)) ?></pre>
                            </td>
                            <td class="text-secondary text-nowrap"><?= escape($data['date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
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
