<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Регистрация<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Регистрация</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<div class="section shadow border p-3">
    <?php if (setting('main.confirm_email')): ?>
        <div class="alert alert-info">
            <i class="bi bi-exclamation-circle-fill"></i>
            Включено подтверждение регистрации!
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="login" class="form-label">Логин</label>
            <input type="text" class="form-control<?= hasError('login') ?>" id="login" name="login" value="<?= old('login') ?>" required>
            <div class="invalid-feedback"><?= getError('login') ?></div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control<?= hasError('password') ?>" id="password" name="password" value="<?= old('password') ?>" required>
            <div class="invalid-feedback"><?= getError('password') ?></div>
        </div>

        <div class="mb-3">
            <label for="password2" class="form-label">Повторите пароль</label>
            <input type="password" class="form-control<?= hasError('password2') ?>" id="password2" name="password2" value="<?= old('password2') ?>" required>
            <div class="invalid-feedback"><?= getError('password2') ?></div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control<?= hasError('email') ?>" id="email" name="email" value="<?= old('email') ?>" required>
            <div class="invalid-feedback"><?= getError('email') ?></div>
        </div>

        <?= $this->fetch('app/_captcha') ?>

        <button type="submit" class="btn btn-primary">Регистрация</button>
    </form>
</div>
