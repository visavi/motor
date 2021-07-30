# Motor

Данный скрипт предоставляет ООП подход для работы текстовыми данными сохраненными в файловой системе

Структура данных очень похожа на CSV с некоторыми изменения для более быстрой работы

### Возможности

- Поиск по уникальному ключу
- Поиск по любым заданным условиям
- Поиск первой записи
- Поиск последней записи
- Возврат структуры файла
- Возврат количества записей в файле
- Запись строки в файл с генерацией автоинкрементного ключа
- Обновление записей по любым условиям
- Удаление записей по любым условиям
- Очистка файла

Работы с изменениями в файле, в том числе и вставка выполняется с блокировкой файла для защиты от случайного удаления данных в случае если несколько пользователей одновременно пишут в файл

Первых столбец в файле считается уникальным

Может быть строковым и числовым

Если столбец строковой, то все вставки должны быть с уже заданным уникальным ключом

Если столбец числовой, то уникальный ключ будет генерироваться автоматически

### Примеры
```php
use App\Reader;

$file = __DIR__ . '/index.csv';

$fileObject = new Reader($file);
echo '<pre>';

echo ' ---- Find by primary key -------<br>';
var_dump($fileObject->find(1));

echo ' ---- Find by name limit 1 -----------<br>';
var_dump($fileObject->where('name', 'Миша')->limit(1)->get());

echo ' ---- Find by name and last 1 -----------<br>';
var_dump($fileObject->where('name', 'Миша')->reverse()->first());

echo ' ---- Find by name and title -----------<br>';
var_dump($fileObject->where('name', 'Миша')->where('title', 'Заголовок10')->get());

echo ' ---- Get from condition -----------<br>';
var_dump($fileObject->where('time', '>=', 1231231235)->get());

echo ' ---- Get by condition in -------<br>';
var_dump($fileObject->whereIn('id', [1,3,4,7])->get());

echo ' ---- Get count -----------<br>';
var_dump($fileObject->where('time', '>', 1231231234)->count());

echo ' ---- Get lines 1 - 10 -----------<br>';
var_dump($lines = $fileObject->offset(0)->limit(10)->get());

echo ' ---- Get lines reverse (last 10 lines reversed)  -----------<br>';
var_dump($lines = $fileObject->reverse()->offset(0)->limit(10)->get());

echo ' ---- Get from condition limit and reverse -----------<br>';
var_dump($lines = $fileObject->where('name', 'Миша')->limit(10)->reverse()->get());

echo ' ---- Get headers -----------<br>';
var_dump($fileObject->headers());

echo ' ---- Get first line -----------<br>';
var_dump($fileObject->first());

echo ' ---- Get first 3 lines -----------<br>';
var_dump($fileObject->limit(3)->get());

echo ' ---- Get last 3 lines -----------<br>';
var_dump($fileObject->reverse()->limit(3)->get());

echo ' ---- Insert string -----------<br>';
var_dump($fileObject->insert(['name' => 'Миша']));

echo ' ---- Update strings -----------<br>';
var_dump($fileObject->where('name', 'Миша')->update(['text' => 'Новый текст']));

echo ' ---- Delete strings -----------<br>';
var_dump($fileObject->where('name', 'Миша')->delete());

echo ' ---- Truncate file -----------<br>';
var_dump($fileObject->truncate());
```
