<?php
/**
 * Пустое состояние списка
 *
 * @var string      $title    заголовок
 * @var string|null $subtitle пояснение, чего ждать
 * @var string|null $icon     класс иконки bootstrap-icons
 * @var string|null $action   подпись кнопки
 * @var string|null $link     адрес кнопки
 */

$subtitle ??= null;
$icon ??= 'bi-inbox';
$action ??= null;
$link ??= null;
?>
<div class="card">
    <div class="empty">
        <div class="empty-icon">
            <i class="bi <?= $icon ?> fs-1"></i>
        </div>

        <p class="empty-title"><?= escape($title) ?></p>

        <?php if ($subtitle): ?>
            <p class="empty-subtitle text-secondary"><?= escape($subtitle) ?></p>
        <?php endif; ?>

        <?php if ($action && $link): ?>
            <div class="empty-action">
                <a href="<?= $link ?>" class="btn btn-primary">
                    <i class="bi bi-plus me-1"></i>
                    <?= escape($action) ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
