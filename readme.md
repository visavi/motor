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

### Запросы

Все запросы проводятся через модели в котором должен быть указан путь к файлу с данными
В самих моделях могут быть реализованы дополнительные методы

### Примеры
```php

# Find by primary key
TestModel::query()->find(1);

# Find by name limit 1
TestModel::query()->where('name', 'Миша')->limit(1)->get();

# Find by name and last 1
TestModel::query()->where('name', 'Миша')->reverse()->first();

# Find by name and title
TestModel::query()->where('name', 'Миша')->where('title', 'Заголовок10')->get();

# Get from condition
TestModel::query()->where('time', '>=', 1231231235)->get();

# Get by condition in
TestModel::query()->whereIn('id', [1, 3, 4, 7])->get();

# Get by condition not in
TestModel::query()->whereNotIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->get();

# Get count
TestModel::query()->where('time', '>', 1231231234)->count();

# Get lines 1 - 10
$lines = TestModel::query()->offset(0)->limit(10)->get();

# Get lines reverse (last 10 lines reversed)
$lines = TestModel::query()->reverse()->offset(0)->limit(10)->get();

# Get from condition limit and reverse
$lines = TestModel::query()->where('name', 'Миша')->limit(10)->reverse()->get();

# Get headers
TestModel::query()->headers();

# Get first line
TestModel::query()->first();

# Get first 3 lines
TestModel::query()->limit(3)->get();

# Get last 3 lines
TestModel::query()->reverse()->limit(3)->get();

# Find by name and double sort (time desc, id asc)
Test::query()
    ->where('name', 'Миша')
    ->orderByDesc('time')
    ->orderBy('id')
    ->limit(3)
    ->get();

# Insert string
TestModel::query()->insert(['name' => 'Миша']);

# Update strings
TestModel::query()->where('name', 'Миша')->update(['text' => 'Новый текст']);

# Update strings
$testModel = TestModel::query()->find(17);
$affectedLines = $testModel->update(['text' => 'Новый текст']);

# Delete strings
TestModel::query()->where('name', 'Миша')->delete();

# Truncate file
TestModel::query()->truncate();
```
