# Motor CMS

A content management system that keeps everything in CSV files. No database
server needed: upload the archive to your hosting, open the site — it creates
its tables itself and runs.

Read this in [Russian](readme.ru.md).

## Features

- articles with comments, tags, ratings, attachments and polls
- a guestbook
- user profiles with avatars, notifications, favourites
- article search and a tag cloud
- "Online now" and site statistics in the sidebar
- light and dark themes
- an admin area: settings, stickers, backups, error log
- user management: role, email, deletion

Built on [Slim](https://www.slimframework.com), [Plates](https://platesphp.com)
and [motor-orm](https://github.com/visavi/motor-orm), with a
[Tabler](https://tabler.io) interface and a [Vite](https://vite.dev) frontend
build. The data lives in `storage/database/*.csv`.

## Who it is for

Small sites and blogs, where a database server takes more attention than the
content does. Any table opens in a spreadsheet editor, and a backup of the
whole site is a zip archive of CSV files.

It does not suit sites where many people write at the same time: a write locks
the table file and rewrites it whole.

## Requirements

- PHP 8.5+ with the `curl`, `gd`, `mbstring` and `zip` extensions
- Apache with `mod_rewrite`, or nginx with its root at `public/`
- to install from the repository, also Composer and Node.js 20.19+ or 22.12+

## Installation

### On hosting without SSH

1. Download `motor-<version>.zip` from the
   [releases page](https://github.com/visavi/motor/releases). The archive already
   holds the dependencies and the built frontend — no Composer or Node.js needed.
2. Upload the contents of the archive's `motor/` folder over FTP.
3. Point the domain at `public/`. If the hosting panel does not allow that,
   leave the project folder itself as the root: `.htaccess` redirects requests
   to `public/`.
4. Open the site. On the first visit it creates its tables and shows
   "Установка завершена" (installation complete) with the next steps.

### On a server with SSH

```bash
git clone https://github.com/visavi/motor.git
cd motor
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php motor migrate
```

- `npm run build` builds the styles and scripts into `public/build`. That folder
  is not kept in the repository, so without the build the site opens unstyled.
- `migrate` creates the tables in `storage/database` and fills in the default
  settings. It can be skipped: the site does the same on its first visit.

Point the web server at `public/`.

Either way, `storage` and `public/uploads` have to be writable by the web
server. If they are not, the site lists the folders that lack permissions.

### The first administrator

Register through `/register`, then make yourself the owner by hand: in
`storage/database/users.csv`, change the `role` column (`user`, or `pended` when
email confirmation is on) to `boss`.

```
id,login,password,email,role,…
1,admin,$2y$12$…,admin@example.com,boss,…
```

Everyone else's role is changed on the site itself, on the user edit page:
`/users/{login}/edit`.

Roles from most to least privileged: `boss` — the owner, `admin`, `moder`,
`editor`, `user`, `pended` — waiting for email confirmation, `banned`. The admin
area is open to the owner only, while roles from `editor` up can edit users and
comments.

## Updating

**Without SSH.** Download the new version's archive and upload the contents of
`motor/` over the old one, replacing files. The site data is safe: the archive
has no files in `storage` and does not overwrite user uploads. Open the site —
the first request backs up the tables into `storage/backups` and applies the new
migrations.

**With SSH.**

```bash
git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php motor backup && php motor migrate
```

If `migrate` is skipped, the site applies the migrations itself on the first
visit, just like an update without SSH.

### If an update fails

The site answers visitors with "Сайт обновляется" (the site is updating) and a
503 code, and does not retry the failed attempt on every request. The owner can
log in and open `/admin/migrations`: it shows the migration, the error, the
backup name and a "Повторить" (retry) button. The full error is in
`storage/logs/motor.log`.

The site also retries by itself once a fixed version is uploaded. To retry
without changing any files, delete `storage/logs/migrate-failed.json`.

To bring the tables back to how they were before the update, unpack the backup
from `storage/backups` into `storage/database`, or run
`php motor backup:restore <archive>`.

## Development

```bash
npm install
npm run dev                         # Vite with hot reload
php -S localhost:8000 -t public     # in another terminal
```

While `npm run dev` is running, the `vite()` helper loads styles and scripts
from the dev server, so changes in `resources/css` and `resources/js` show up at
once, with no rebuild. Once it stops, the site goes back to the build in
`public/build`.

Detailed errors are shown when `displayErrorDetails` is on in
`app/settings.php`. On a live site it stays off, and errors are read from
`storage/logs/motor.log` — also available at `/admin/logs`.

## Structure

```
app/
    Commands/       console commands
    Controllers/    request handlers, Admin/ and User/ among them
    Middleware/     session, authentication, access, trailing slash
    Models/         table models: columns, types, relations, row methods
    Repositories/   the queries controllers need
    Services/       bbcode, mail, captcha, slugs, validation, templates
    helpers.php     global functions: route(), setting(), escape(), vite()…
    routes.php      the routes of the site
    settings.php    application and logging settings
database/
    migrations/     schema changes, applied by the site or `php motor migrate`
resources/
    css/            own styles and the palette (theme.css)
    js/             scripts, app.js is the entry point
    views/          Plates templates
public/             the document root: index.php, uploads, build
storage/
    database/       the site data in CSV
    backups/        archives made by `php motor backup`
    logs/           the error log
```

## Console commands

```bash
php motor migrate                   # apply new migrations
php motor migrate:rollback          # roll back the last migration run
php motor backup                    # pack storage/database into storage/backups
php motor backup:restore <archive>  # restore the data from an archive
```

The site checks the files in `database/migrations` against the applied
migrations on every visit. So after `migrate:rollback`, the rolled-back
migration is applied again on the very next request, as long as its file is
still there. To keep a rollback, delete or fix the migration file.

## Working with data

Every table is described by a model. The model names the file, the column types
and the relations, and a model that has been read is one row of the table, with
methods of its own:

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

Conditions, sorting and pagination belong to the query:

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

Controllers do not build queries: repositories hold them, and the container
passes the repository to the constructor:

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

The ORM guide — models, queries, relations, collections, pagination and
migrations — is served by the site itself at `/docs` (in Russian), and the full
description of the library lives in the
[motor-orm](https://github.com/visavi/motor-orm) repository.

## Migrations

A migration is a file in `database/migrations` named like
`2026_08_28_120000_add_parent_id_to_comments.php`. The date at the start sets
the order they run in. The file returns an object with `up()` and `down()`:

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

Every change listed before `changeTable()` is applied in a single pass over the
file.

New migrations are applied on the first visit after an update, with a backup of
the tables made first. The migration history and a manual run live at
`/admin/migrations`.

## Settings

Settings are rows of `settings.csv`, edited in the admin area at
`/admin/settings` and read from anywhere through `setting()`:

```php
setting('story.per_page');
setting('main.allow_register');
```

They set the site name, registration and email confirmation, the length of
articles, comments and messages, upload limits, image sizes, the captcha and how
many items fit on a page.

## Templates and styling

Plates templates live in `resources/views`: the layouts, the header, sidebar
and footer, and a folder for each section. The markup is built from Tabler
components.

The site colours are variables in `resources/css/theme.css`, one set for the
light theme and one for the dark. Buttons, links, badges and code highlighting
pick up the Tabler variables, so changing the palette means changing their
values and rebuilding the frontend.

Page navigation is printed by a helper:

```php
<?= pagination($stories) ?>
```

It renders `resources/views/app/_paginator.php` — that is where the markup is
changed.

## Deployment

`deploy.yaml` is a [Deployer](https://deployer.org) recipe. It installs the
dependencies, builds the frontend, backs up the data, runs the migrations and
switches the site to the new release. `storage` and `public/uploads` are shared
between releases.

```bash
vendor/bin/dep deploy
```

### Release archive

The archive for installing without SSH is built by GitHub Actions
(`.github/workflows/release.yml`) when a tag like `v1.2.3` is pushed: it
installs the dependencies without dev packages, builds the frontend, packs the
project into `motor-v1.2.3.zip` and attaches it to the release. Before the
upload the archive is unpacked and checked to install a working site.

To build and check the archive locally, after `composer install --no-dev` and
`npm run build`:

```bash
.github/release.sh v1.2.3
```

## License

GPL-3.0-only
