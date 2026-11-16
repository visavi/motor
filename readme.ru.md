# Motor CMS

Система управления контентом, которая хранит всё в CSV-файлах. Сервер базы
данных не нужен: залили архив на хостинг, открыли сайт — он сам создал таблицы
и работает.

Read this in [English](readme.md).

## Возможности

- статьи с комментариями, тегами, рейтингом, вложениями и опросами
- гостевая книга
- профили пользователей с аватарами, уведомления, избранное
- поиск по статьям и облако тегов
- «Сейчас на сайте» и статистика в сайдбаре
- светлая и тёмная тема
- админка: настройки, стикеры, резервные копии, журнал ошибок
- управление пользователями: роль, почта, удаление

Внутри — [Slim](https://www.slimframework.com), [Plates](https://platesphp.com)
и [motor-orm](https://github.com/visavi/motor-orm), интерфейс на
[Tabler](https://tabler.io), сборка фронтенда — [Vite](https://vite.dev).
Данные лежат в `storage/database/*.csv`.

## Кому подходит

Небольшим сайтам и блогам, где сервер базы данных требует больше внимания, чем
сам контент. Таблицу можно открыть в любом редакторе таблиц, а резервная копия
сайта — это zip-архив с CSV-файлами.

Не подходит сайтам, где много людей пишут одновременно: запись блокирует файл
таблицы и перезаписывает его целиком.

## Требования

- PHP 8.5+ с расширениями `curl`, `gd`, `mbstring`, `zip`
- Apache с `mod_rewrite` либо nginx с корнем в `public/`
- для установки из репозитория ещё Composer и Node.js 20.19+ или 22.12+

## Установка

### На хостинг без SSH

1. Скачайте `motor-<версия>.zip` со
   [страницы релизов](https://github.com/visavi/motor/releases). В архиве уже есть
   зависимости и собранный фронтенд — Composer и Node.js не нужны.
2. Залейте содержимое папки `motor/` из архива на хостинг по FTP.
3. Направьте домен на папку `public/`. Если панель хостинга этого не позволяет,
   оставьте корнем саму папку проекта: `.htaccess` перенаправит запросы в `public/`.
4. Откройте сайт. При первом открытии он сам создаст таблицы и покажет
   «Установка завершена» с дальнейшими шагами.

### На сервер с SSH

```bash
git clone https://github.com/visavi/motor.git
cd motor
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php motor migrate
```

- `npm run build` собирает стили и скрипты в `public/build`. Эта папка не хранится
  в репозитории, поэтому без сборки сайт откроется без оформления.
- `migrate` создаёт таблицы в `storage/database` и заполняет настройки по
  умолчанию. Его можно и не запускать: сайт сделает это сам при первом открытии.

Веб-сервер направьте на `public/`.

В обоих случаях папки `storage` и `public/uploads` должны быть доступны
веб-серверу на запись. Если прав не хватает, сайт при открытии покажет, каким
именно папкам.

### Первый администратор

Зарегистрируйтесь через `/register`, а затем назначьте себя владельцем вручную:
в `storage/database/users.csv` замените значение колонки `role` (`user`, или
`pended`, если включено подтверждение почты) на `boss`.

```
id,login,password,email,role,…
1,admin,$2y$12$…,admin@example.com,boss,…
```

Роли остальных пользователей меняются уже на сайте, на странице редактирования
пользователя: `/users/{login}/edit`.

Роли по убыванию прав: `boss` — владелец, `admin`, `moder`, `editor`, `user`,
`pended` — ждёт подтверждения почты, `banned` — заблокирован. Админка доступна
только владельцу, а редактировать пользователей и комментарии могут роли от
`editor` и выше.

## Обновление

**Без SSH.** Скачайте архив новой версии и залейте содержимое `motor/` поверх
старой с заменой файлов. Данные сайта не пострадают: в архиве нет файлов в
`storage`, а загрузки пользователей он не перезаписывает. Откройте сайт — первый
запрос сделает бэкап таблиц в `storage/backups` и применит новые миграции.

**С SSH.**

```bash
git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php motor backup && php motor migrate
```

Если пропустить `migrate`, сайт сам применит миграции при первом открытии, как и
при обновлении без SSH.

### Если обновление не удалось

Сайт отвечает посетителям «Сайт обновляется» с кодом 503 и не повторяет
неудачную попытку на каждом запросе. Владелец может войти на сайт и открыть
`/admin/migrations`: там имя миграции, текст ошибки, имя бэкапа и кнопка
«Повторить». Полный текст ошибки — в `storage/logs/motor.log`.

Сайт попробует снова и сам, если вы зальёте исправленную версию. Чтобы повторить
без изменений в файлах, удалите `storage/logs/migrate-failed.json`.

Чтобы вернуть таблицы к состоянию до обновления, распакуйте бэкап из
`storage/backups` в `storage/database` или выполните
`php motor backup:restore <архив>`.

## Разработка

```bash
npm install
npm run dev                         # Vite с горячей перезагрузкой
php -S localhost:8000 -t public     # в соседнем терминале
```

Пока работает `npm run dev`, хелпер `vite()` берёт стили и скрипты с dev-сервера:
изменения в `resources/css` и `resources/js` видны сразу, без пересборки.
После остановки сайт снова использует сборку из `public/build`.

Подробные ошибки показываются, если в `app/settings.php` включён
`displayErrorDetails`. На рабочем сайте его оставляют выключенным, а ошибки
читают в журнале `storage/logs/motor.log` — он же доступен по адресу
`/admin/logs`.

## Структура

```
app/
    Commands/       консольные команды
    Controllers/    обработчики запросов, в том числе Admin/ и User/
    Middleware/     сессия, авторизация, доступ, слеш в конце адреса
    Models/         модели таблиц: колонки, типы, связи, методы строки
    Repositories/   запросы, которые нужны контроллерам
    Services/       bbcode, почта, капча, слаги, валидация, шаблоны
    helpers.php     глобальные функции: route(), setting(), escape(), vite()…
    routes.php      маршруты сайта
    settings.php    настройки приложения и логирования
database/
    migrations/     изменения схемы, применяются сами или `php motor migrate`
resources/
    css/            свои стили и палитра (theme.css)
    js/             скрипты, точка входа — app.js
    views/          шаблоны Plates
public/             корень сайта: index.php, uploads, build
storage/
    database/       данные сайта в CSV
    backups/        архивы, созданные `php motor backup`
    logs/           журнал ошибок
```

## Консольные команды

```bash
php motor migrate                   # применить новые миграции
php motor migrate:rollback          # откатить последний запуск миграций
php motor backup                    # упаковать storage/database в storage/backups
php motor backup:restore <архив>    # восстановить данные из архива
```

Сайт сверяет файлы в `database/migrations` с выполненными миграциями при каждом
открытии. Поэтому после `migrate:rollback` откаченная миграция применится снова
при первом же запросе к сайту, если её файл остался на месте. Чтобы откат
держался, удалите или исправьте файл миграции.

## Работа с данными

Каждая таблица описывается моделью. Модель указывает файл, типы колонок и связи,
а прочитанная модель — это одна строка таблицы со своими методами:

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

Условия, сортировка и пагинация задаются в запросе:

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

Контроллеры запросов не строят: их держат репозитории, а контейнер передаёт
репозиторий в конструктор:

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

Документация по ORM — модели, запросы, связи, коллекции, пагинация и миграции —
открывается на самом сайте по адресу `/docs`, полное описание библиотеки лежит
в репозитории [motor-orm](https://github.com/visavi/motor-orm).

## Миграции

Миграция — файл в `database/migrations` с именем вида
`2026_08_28_120000_add_parent_id_to_comments.php`. Дата в начале задаёт порядок
выполнения. Файл возвращает объект с методами `up()` и `down()`:

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

Все изменения до `changeTable()` применяются за один проход по файлу.

Новые миграции применяются при первом открытии сайта после обновления, с бэкапом
таблиц перед ними. История миграций и ручной запуск — на странице
`/admin/migrations`.

## Настройки

Настройки хранятся в `settings.csv`, редактируются в админке по адресу
`/admin/settings` и читаются из любого места через `setting()`:

```php
setting('story.per_page');
setting('main.allow_register');
```

Через них задаются название сайта, регистрация и подтверждение почты, длина
статей, комментариев и сообщений, ограничения загрузок, размеры картинок, капча
и число элементов на странице.

## Шаблоны и оформление

Шаблоны Plates лежат в `resources/views`: общие макеты, шапка, сайдбар, подвал и
папка на каждый раздел. Разметка построена на компонентах Tabler.

Цвета сайта задаются переменными в `resources/css/theme.css` — отдельно для
светлой и тёмной темы. Переменные Tabler подхватывают кнопки, ссылки, бейджи и
подсветку кода, поэтому для смены палитры достаточно поменять их значения и
пересобрать фронтенд.

Постраничную навигацию выводит хелпер:

```php
<?= pagination($stories) ?>
```

Он рендерит `resources/views/app/_paginator.php` — разметка меняется там.

## Развёртывание

`deploy.yaml` — рецепт [Deployer](https://deployer.org). Он ставит зависимости,
собирает фронтенд, делает резервную копию данных, выполняет миграции и
переключает сайт на новый релиз. Папки `storage` и `public/uploads` общие для
всех релизов.

```bash
vendor/bin/dep deploy
```

### Архив релиза

Архив для установки без SSH собирает GitHub Actions
(`.github/workflows/release.yml`) при пуше тега вида `v1.2.3`: ставит
зависимости без dev-пакетов, собирает фронтенд, упаковывает проект в
`motor-v1.2.3.zip` и прикладывает его к релизу. Перед загрузкой архив
распаковывается и проверяется, что сайт из него устанавливается.

Собрать и проверить архив локально, после `composer install --no-dev` и
`npm run build`:

```bash
.github/release.sh v1.2.3
```

## Лицензия

GPL-3.0-only
