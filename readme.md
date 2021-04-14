#Motor

Пока это продвинутая читалка csv

```php
use App\Reader;

$file = __DIR__ . '/index.csv';

$fileObject = new Reader($file);
echo '<pre>';

echo ' ---- Find by id -------<br>';
var_dump($fileObject->find('Vantuz'));

echo ' ---- Find by name limit 1 -----------<br>';
var_dump($fileObject->where('name', 'Миша')->limit(1)->get());

echo ' ---- Find by name and last 1 -----------<br>';
var_dump($fileObject->where('name', 'Миша')->reverse()->first());

echo ' ---- Find by name and title -----------<br>';
var_dump($fileObject->where('name', 'Миша')->where('title', 'Заголовок10')->get());

echo ' ---- Get from condition -----------<br>';
var_dump($fileObject->where('time', '>=', 1231231235)->get());

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
var_dump($fileObject->first(3));

echo ' ---- Get last 3 lines -----------<br>';
var_dump($fileObject->reverse()->first(3));
```
