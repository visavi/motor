<?php $this->layout('layout') ?>

<?php $this->start('title') ?>Вход<?php $this->stop() ?>

<?php $this->start('breadcrumb') ?>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active">Авторизация</li>
        </ol>
    </nav>
<?php $this->stop() ?>

<div class="section p-3 shadow">
    <form method="post">
        <div class="mb-3">
            <label for="login" class="form-label">Логин</label>
            <input type="text" class="form-control<?= hasError('login') ?>" id="login" name="login" value="<?= old('login') ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control<?= hasError('password') ?>" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary">Войти</button>
    </form>
</div>
