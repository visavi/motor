<div class="mb-3">
    <label for="captcha" class="form-label">Проверочный код:</label><br>
    <img src="/captcha" onclick="this.src='/captcha?'+Math.random()" class="rounded" alt="Captcha" style="cursor: pointer"><br>
    <input class="form-control<?= hasError('captcha') ?>" name="captcha" id="captcha" maxlength="8" autocomplete="off" required>
    <div class="invalid-feedback"><?= getError('captcha') ?></div>
</div>
