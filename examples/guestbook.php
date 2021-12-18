<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Test;
use App\Paginator;
?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/src/css/bootstrap.css">
<?php

$action = htmlspecialchars($_GET['action'] ?? 'index');

if ($action === 'index') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $text = preg_replace('/\R/u', '\n', $_POST['text']);

        Test::query()->insert([
            'name' => htmlspecialchars($_POST['name']),
            'title' => htmlspecialchars($_POST['title']),
            'text' => $text,
            'time' => time(),
        ]);

        header('location: guestbook.php'); exit;
    }

    $total = Test::query()->count();
    $paginator = new Paginator($total);
    $messages = Test::query()->reverse()->offset($paginator->offset)->limit($paginator->limit)->get();

    if ($messages) {
        foreach ($messages as $message) {
            echo '<div>' . $message->name . ' (' . date('Y-m-d H:i', $message->time) . ')<br>' . $message->title . '<br>' . nl2br(stripcslashes(htmlspecialchars($message->text))) . ' 

    <a href="?action=edit&amp;id=' . $message->id . '">Edit</a>
    <a href="?action=delete&amp;id=' . $message->id . '">Del</a>
</div><hr>';
        }

        echo $paginator->links();
    } else {
        echo 'Сообщений нет<br>';
    }

    echo '<div class="row mb-3 shadow">
        <form method="post">
          <div class="mb-3">
            <label for="name" class="form-label">Имя</label>
            <input type="text" class="form-control" id="name" name="name">
          </div>
          <div class="mb-3">
            <label for="title" class="form-label">Заголовок</label>
            <input type="text" class="form-control" id="title" name="title">
          </div>
            <div class="mb-3">
              <label for="text" class="form-label">Текст</label>
              <textarea class="form-control" id="text" rows="3" name="text"></textarea>
            </div>
          <button type="submit" class="btn btn-primary">Отправить</button>
        </form>
    </div>';
}

if ($action === 'edit') {
    $message = Test::query()->find((int) $_GET['id']);
    if ($message) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $text = preg_replace('/\R/u', '\n', $_POST['text']);

            $message->update([
                'name' => htmlspecialchars($_POST['name']),
                'title' => htmlspecialchars($_POST['title']),
                'text' => $text,
            ]);

            header('location: guestbook.php'); exit;
        }

        echo '<div class="row mb-3 shadow">
            <form method="post">
              <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input type="text" class="form-control" id="name" name="name" value="' . $message->name . '">
              </div>
              <div class="mb-3">
                <label for="title" class="form-label">Заголовок</label>
                <input type="text" class="form-control" id="title" name="title" value="' . $message->title . '">
              </div>
                <div class="mb-3">
                  <label for="text" class="form-label">Текст</label>
                  <textarea class="form-control" id="text" rows="3" name="text">' . $message->text . '</textarea>
                </div>
              <button type="submit" class="btn btn-primary">Изменить</button>
            </form>
        </div>';

    } else {
        echo 'Сообщение не найдено';
    }
}

if ($action === 'delete') {
    $message = Test::query()->find((int) $_GET['id']);
    $message?->delete();

    header('location: guestbook.php'); exit;
}
