<div class="mb-3">
    <label for="captcha" class="form-label">Проверочный код:</label>
    <div class="d-flex align-items-start gap-2">
        <div style="width: 8rem">
            <input class="form-control<?= hasError('captcha') ?>" name="captcha" id="captcha" maxlength="8" autocomplete="off" placeholder="Код" required>
            <div class="invalid-feedback"><?= getError('captcha') ?></div>
        </div>
        <img src="/captcha" onclick="this.src='/captcha?'+Math.random()" class="rounded" alt="Captcha" title="Обновить" style="cursor: pointer">
    </div>
</div>
