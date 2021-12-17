<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Test;
use App\Paginator;

?>
    <link rel="stylesheet" type="text/css" href="/src/css/bootstrap.css">
<?php

$file = __DIR__ . '/../tests/data/test.csv';

$action = htmlspecialchars($_GET['action'] ?? 'index');

if ($action === 'index') {
    $perPage = 3;
    $total = Test::query()->count();

    $paginator = new Paginator($perPage, $total);

    $messages = Test::query()->reverse()->offset($paginator->offset)->limit($paginator->limit)->get();

    if ($messages) {
        foreach ($messages as $message) {
            echo '<div>' . $message->name . ' (' . date('Y-m-d H:i', $message->time) . ')<br>' . $message->title . '<br>' . nl2br(stripcslashes(htmlspecialchars($message->text))) . ' 

    <a href="?action=edit&amp;id=' . $message->id . '">Edit</a>
    <a href="?action=delete&amp;id=' . $message->id . '">Del</a>
</div><hr>';
        }

        echo $paginator->links();

        echo '<form method="post">
  Имя<br>
  <input name="name"><br>
  Заголовок<br>
  <input name="title"><br>
  Текст<br>
  <textarea name="text"></textarea><br>
  <button>Отправить</button>
 </form>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $text = preg_replace('/\R/u', '\n', $_POST['text']);

            Test::query()->insert([
                'name' => htmlspecialchars($_POST['name']),
                'title' => htmlspecialchars($_POST['title']),
                'text' => $text,
                'time' => time(),
            ]);

            header('location: guestbook.php');
        }


    } else {
        echo 'Сообщений нет<br>';
    }
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

            header('location: guestbook.php');
        }

        echo '<form method="post">
  Имя<br>
  <input name="name" value="' . $message->name . '"><br>
  Заголовок<br>
  <input name="title" value="' . $message->title . '"><br>
  Текст<br>
  <textarea name="text">' . $message->text . '</textarea><br>
  <button>Изменить</button>
 </form>';
    } else {echo 'Сообщение не найдено';}
}


if ($action === 'delete') {
    $message = Test::query()->find((int) $_GET['id']);

    if ($message) {
        $message->delete();
    }

    header('location: guestbook.php');
}

