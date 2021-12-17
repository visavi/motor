<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Test;

echo '<pre>';

echo ' ---- Find by primary key -------<br>';
var_dump(Test::query()->find(1));

echo ' ---- Find by name limit 1 -----------<br>';
var_dump(Test::query()->where('name', 'Миша')->limit(1)->get());

echo ' ---- Find by name and last 1 -----------<br>';
var_dump(Test::query()->where('name', 'Миша')->reverse()->first());

echo ' ---- Find by name and title -----------<br>';
var_dump(Test::query()->where('name', 'Миша')->where('title', 'Заголовок10')->get());

echo ' ---- Get from condition -----------<br>';
var_dump(Test::query()->where('time', '>=', 1231231235)->get());

echo ' ---- Get by condition in -------<br>';
var_dump(Test::query()->whereIn('id', [1, 3, 4, 7])->get());

echo ' ---- Get by condition not in -------<br>';
var_dump(Test::query()->whereNotIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->get());

echo ' ---- Get count -----------<br>';
var_dump(Test::query()->where('time', '>', 1231231234)->count());

echo ' ---- Get lines 1 - 10 -----------<br>';
var_dump($lines = Test::query()->offset(0)->limit(10)->get());

echo ' ---- Get lines reverse (last 10 lines reversed)  -----------<br>';
var_dump($lines = Test::query()->reverse()->offset(0)->limit(10)->get());

echo ' ---- Get from condition limit and reverse -----------<br>';
var_dump($lines = Test::query()->where('name', 'Миша')->limit(10)->reverse()->get());

echo ' ---- Get headers -----------<br>';
var_dump(Test::query()->headers());

echo ' ---- Get first line -----------<br>';
var_dump(Test::query()->first());

echo ' ---- Get first 3 lines -----------<br>';
var_dump(Test::query()->limit(3)->get());

echo ' ---- Get last 3 lines -----------<br>';
var_dump(Test::query()->reverse()->limit(3)->get());
