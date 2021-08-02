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

$reader = new Reader($file);

# Find by primary key
$reader->find(1);

# Find by name limit 1
$reader->where('name', 'Миша')->limit(1)->get();

# Find by name and last 1
$reader->where('name', 'Миша')->reverse()->first();

# Find by name and title
$reader->where('name', 'Миша')->where('title', 'Заголовок10')->get();

# Get from condition
$reader->where('time', '>=', 1231231235)->get();

# Get by condition in
$reader->whereIn('id', [1,3,4,7])->get();

# Get count
$reader->where('time', '>', 1231231234)->count();

# Get lines 1 - 10
$lines = $reader->offset(0)->limit(10)->get();

# Get lines reverse (last 10 lines reversed)
$lines = $reader->reverse()->offset(0)->limit(10)->get();

# Get from condition limit and reverse
$lines = $reader->where('name', 'Миша')->limit(10)->reverse()->get();

# Get headers
$reader->headers();

# Get first line
$reader->first();

# Get first 3 lines
$reader->limit(3)->get();

# Get last 3 lines
$reader->reverse()->limit(3)->get();

# Insert string
$reader->insert(['name' => 'Миша']);

# Update strings
$reader->where('name', 'Миша')->update(['text' => 'Новый текст']);

# Delete strings
$reader->where('name', 'Миша')->delete();

# Truncate file
$reader->truncate();
```
