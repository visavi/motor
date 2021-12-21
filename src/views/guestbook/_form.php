<div class="p-3 shadow">
    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Имя</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= $message->name ?? null ?>">
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">Заголовок</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= $message->title ?? null ?>">
        </div>
        <div class="mb-3">
            <label for="text" class="form-label">Текст</label>
            <textarea class="form-control" id="text" rows="3" name="text"><?= $message->text ?? null ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary"><?= isset($message) ? 'Изменить' : 'Отправить' ?></button>
    </form>
</div>
