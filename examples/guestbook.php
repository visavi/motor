<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use App\Paginator;
use App\Reader;

?>
    <link rel="stylesheet" type="text/css" href="/src/css/bootstrap.css">
<?php

$file = __DIR__ . '/../tests/data/test.csv';

$reader = new Reader($file);

$perPage = 3;
$total = $reader->count();

$paginator = new Paginator($perPage, $total);

$messages = $reader->reverse()->offset($paginator->offset)->limit($paginator->limit)->get();

if ($messages) {
    foreach ($messages as $message) {
        echo '<div>' . $message['name'] . ' (' . date('Y-m-d H:i', $message['time']) . ')<br>' . $message['title'] . '<br>' . $message['text'] . ' </div><hr>';
    }

    echo $paginator->links();
} else {
    echo 'Сообщений нет';
}




