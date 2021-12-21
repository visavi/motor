<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Test;
use App\Paginator;
use App\View;

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
    $messages = Test::query()
        ->reverse()
        ->offset($paginator->offset)
        ->limit($paginator->limit)
        ->get();

    echo (new View())->render('guestbook/index', compact('messages', 'paginator'));
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

        echo (new View())->render('guestbook/edit', compact('message'));

    } else {
        echo 'Сообщение не найдено';
    }
}

if ($action === 'delete') {
    $message = Test::query()->find((int) $_GET['id']);
    $message?->delete();

    header('location: guestbook.php'); exit;
}
