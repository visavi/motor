# Motor CMS

Система управления контентом, которая держит всё в csv-файлах. Никакого сервера
базы данных ставить, настраивать и обслуживать не нужно: распаковали файлы,
выполнили миграции — сайт работает.

Read this in [English](readme.md).

## Что это

Статьи с комментариями, тегами, рейтингом и вложениями, гостевая книга, профили
пользователей с аватарами, уведомления, избранное, поиск и админка — на
[Slim](https://www.slimframework.com), [Plates](https://platesphp.com) и
[motor-orm](https://github.com/visavi/motor-orm), с таблицами в
`storage/database/*.csv`.

Подходит небольшим сайтам и блогам, где сервер базы данных требует больше
внимания, чем сам контент. Не подходит там, где много людей пишут одновременно:
запись перезаписывает файл таблицы целиком.

## Требования

- PHP 8.5+
- расширения: `curl`, `mbstring`, `zip`, `gd`
- Composer
- Apache с `mod_rewrite` либо nginx, смотрящий в `public/`

## Установка

```bash
git clone https://github.com/visavi/motor.git
cd motor
composer install --no-dev --optimize-autoloader
php motor migrate
```

`migrate` создаёт таблицы в `storage/database` и заполняет настройки. Веб-сервер
направьте на `public/`; лежащий в корне `.htaccess` уже перенаправляет туда,
если корнем документов оказался сам проект.

`storage` и `public/uploads` должны быть доступны веб-серверу на запись.

Первую учётную запись заведите через `/register`, а владельцем сделайте её
руками: роль — пятая колонка `storage/database/users.csv`, там `user` меняется
на `boss`.

```
id,login,password,email,role,…
1,admin,$2y$12$…,admin@example.com,boss,…
```

Дальше все остальные учётные записи управляются из админки.

## Структура

```
app/
    Commands/       консольные команды
    Controllers/    обработчики запросов, среди них Admin/ и User/
    Middleware/     сессия, авторизация, доступ, слеш в конце адреса
    Models/         таблица и её строка: колонки, касты, связи, поведение
    Repositories/   запросы, нужные контроллерам
    Services/       bbcode, почта, капча, слаги, валидация, шаблоны
    routes.php      все маршруты сайта
    settings.php    настройки, прочитанные из таблицы, и логирование
database/
    migrations/     схема, применяется командой `php motor migrate`
    tables/         пустые таблицы, с которых начинается установка
public/             корень документов: index.php, assets, uploads
resources/views/    шаблоны plates
storage/
    database/       сами данные, csv
    backups/        zip-архивы, созданные `php motor backup`
    logs/           motor.log, читается из админки
```

## Консоль

```bash
php motor migrate           # применить новые миграции
php motor migrate:rollback  # откатить последнюю партию
php motor backup            # упаковать storage/database в storage/backups
php motor backup:restore <архив>   # вернуть данные из архива
```

## Данные

Модель объявляет свою таблицу и является её строкой. Колонки задаёт файл, касты
превращают строки csv в нужные типы, а всё, что строка умеет ответить о себе, —
метод модели:

```php
class Story extends Model
{
    protected string $table = 'stories.csv';

    protected array $casts = [
        'active'     => 'bool',
        'rating'     => 'int',
        'user_id'    => 'int',
        'created_at' => 'int',
    ];

    public function user(): Relation
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function comments(): Relation
    {
        return $this->hasMany(Comment::class);
    }

    public function getLink(): string
    {
        return route('story-view', ['slug' => sprintf('%s-%d', $this->slug, $this->id)]);
    }
}
```

Читает запрос, а не строка:

```php
$stories = Story::query()
    ->active()
    ->where('created_at', '<', time())
    ->orderByDesc('created_at')
    ->with(['user', 'comments'])
    ->paginate(setting('story.per_page'));

foreach ($stories as $story) {
    echo $story->getLink(), $story->user->getName();
}
```

Контроллеры запросов не строят: запросы страницы держит репозиторий, а его
передаёт контейнер:

```php
class StoryController extends Controller
{
    public function __construct(
        protected View $view,
        protected StoryRepository $storyRepository,
    ) {}

    public function index(Response $response): Response
    {
        $stories = $this->storyRepository->getStories(setting('story.per_page'));

        return $this->view->render($response, 'stories/index', compact('stories'));
    }
}
```

Полное описание orm — в [motor-orm](https://github.com/visavi/motor-orm), сайт
отдаёт его ещё и по адресу `/docs`.

## Изменения схемы

Миграция называет модель и колонки и применяет их одним проходом:

```php
use App\Models\Comment;
use MotorORM\Migration;

return new class
{
    public function up(): void
    {
        new Migration(new Comment())
            ->create('parent_id')->default(0)->after('story_id')
            ->changeTable();
    }

    public function down(): void
    {
        new Migration(new Comment())
            ->delete('parent_id')
            ->changeTable();
    }
};
```

Файлы называются `Y_m_d_His_что_делает.php` и лежат в `database/migrations`.

## Настройки

Настройки — строки `settings.csv`, правятся в админке по адресу
`/admin/settings` и читаются откуда угодно через `setting()`:

```php
setting('story.per_page');
setting('main.allow_register');
```

Ими задаются название и заголовок сайта, регистрация и подтверждение почты,
длины статей, комментариев и сообщений гостевой, ограничения загрузок, размеры
картинок, капча и сколько элементов помещается на страницу.

## Шаблоны

Plates, в `resources/views`. Макет, части для шапки, сайдбара и подвала, и папка
на каждый раздел. Постраничную навигацию печатает приложение:

```php
<?= pagination($stories) ?>
```

этот вызов рендерит `resources/views/app/_paginator.php` — там и меняется
разметка.

## Развёртывание

`deploy.yaml` — рецепт [Deployer](https://deployer.org): `storage` и
`public/uploads` общие для всех релизов, `.env` лежит вне репозитория.

```bash
vendor/bin/dep deploy
```

## Разработка

```bash
php -S localhost:8000 -t public
```

Ошибки показываются, когда в `app/settings.php` включён `displayErrorDetails`;
на бою его оставляют выключенным, а лог пишется в `storage/logs/motor.log` и
читается по адресу `/admin/logs`.

## Лицензия

GPL-3.0-only
