<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use App\Reader;

$file = __DIR__ . '/tests/data/test.csv';

$reader = new Reader($file);
echo '<pre>';

echo ' ---- Find by primary key -------<br>';
var_dump($reader->find(1));

echo ' ---- Find by name limit 1 -----------<br>';
var_dump($reader->where('name', 'Миша')->limit(1)->get());

echo ' ---- Find by name and last 1 -----------<br>';
var_dump($reader->where('name', 'Миша')->reverse()->first());

echo ' ---- Find by name and title -----------<br>';
var_dump($reader->where('name', 'Миша')->where('title', 'Заголовок10')->get());

echo ' ---- Get from condition -----------<br>';
var_dump($reader->where('time', '>=', 1231231235)->get());

echo ' ---- Get by condition in -------<br>';
var_dump($reader->whereIn('id', [1,3,4,7])->get());

echo ' ---- Get count -----------<br>';
var_dump($reader->where('time', '>', 1231231234)->count());

echo ' ---- Get lines 1 - 10 -----------<br>';
var_dump($lines = $reader->offset(0)->limit(10)->get());

echo ' ---- Get lines reverse (last 10 lines reversed)  -----------<br>';
var_dump($lines = $reader->reverse()->offset(0)->limit(10)->get());

echo ' ---- Get from condition limit and reverse -----------<br>';
var_dump($lines = $reader->where('name', 'Миша')->limit(10)->reverse()->get());

echo ' ---- Get headers -----------<br>';
var_dump($reader->headers());

echo ' ---- Get first line -----------<br>';
var_dump($reader->first());

echo ' ---- Get first 3 lines -----------<br>';
var_dump($reader->limit(3)->get());

echo ' ---- Get last 3 lines -----------<br>';
var_dump($reader->reverse()->limit(3)->get());
