# Motor CMS

A content management system that keeps everything in csv files. No database
server to install, configure or back up: unpack the files, run the migrations
and the site is up.

Read this in [Russian](readme.ru.md).

## What it is

Articles with comments, tags, ratings and attachments, a guestbook, user
profiles with avatars, notifications, favourites, a search and an admin area —
built on [Slim](https://www.slimframework.com), [Plates](https://platesphp.com)
and [motor-orm](https://github.com/visavi/motor-orm), with tables living in
`storage/database/*.csv`.

It suits small sites and blogs, where a database server costs more attention
than the content does. It does not suit sites where many people write at the
same time: a write rewrites the whole table file.

## Requirements

- PHP 8.5+
- extensions: `curl`, `mbstring`, `zip`, `gd`
- Composer
- Apache with `mod_rewrite`, or nginx pointed at `public/`

## Installation

```bash
git clone https://github.com/visavi/motor.git
cd motor
composer install --no-dev --optimize-autoloader
php motor migrate
```

`migrate` creates the tables in `storage/database` and fills in the settings.
Point the web server at `public/`; the `.htaccess` in the root already
redirects there when the document root is the project itself.

`storage` and `public/uploads` have to be writable by the web server.

Register the first account through `/register`, then make it the owner by hand:
the role is the fifth column of `storage/database/users.csv`, and `user` there
becomes `boss`.

```
id,login,password,email,role,…
1,admin,$2y$12$…,admin@example.com,boss,…
```

Every other account is then managed from the admin area.

## Layout

```
app/
    Commands/       console commands
    Controllers/    request handlers, Admin/ and User/ among them
    Middleware/     session, authentication, access, trailing slash
    Models/         a table and a row of it: columns, casts, relations, behaviour
    Repositories/   the queries a controller needs
    Services/       bbcode, mail, captcha, slugs, validation, views
    routes.php      every route of the site
    settings.php    settings read out of the table, plus logging
database/
    migrations/     the schema, applied by `php motor migrate`
    tables/         empty tables a fresh install starts from
public/             document root: index.php, assets, uploads
resources/views/    plates templates
storage/
    database/       the data itself, csv
    backups/        zip archives made by `php motor backup`
    logs/           motor.log, readable from the admin area
```

## Console

```bash
php motor migrate           # apply new migrations
php motor migrate:rollback  # roll the last batch back
php motor backup            # zip storage/database into storage/backups
php motor backup:restore <archive>   # put a backup back
```

## Data

A model declares its table and is a row of it. Columns are named by the file,
casts turn the strings of csv into the types the code expects, and whatever a
row can answer about itself is a method of the model:

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

Reading is asked of a query, never of a row:

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

Controllers do not query directly — a repository holds the queries a page
needs, and is handed to the controller by the container:

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

The full orm reference is in [motor-orm](https://github.com/visavi/motor-orm),
and the site serves it at `/docs` as well.

## Schema changes

A migration names the model and the columns, and applies them in one pass:

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

Files are named `Y_m_d_His_what_it_does.php` and live in `database/migrations`.

## Settings

Settings are rows of `settings.csv`, edited in the admin area under
`/admin/settings` and read anywhere through `setting()`:

```php
setting('story.per_page');
setting('main.allow_register');
```

They cover the name and title of the site, registration and email confirmation,
lengths of articles, comments and guestbook messages, upload limits, image
sizes, the captcha and how much fits on a page.

## Templates

Plates, in `resources/views`. A layout, partials for the header, sidebar and
footer, and a folder per section. Pagination is printed by the application:

```php
<?= pagination($stories) ?>
```

which renders `resources/views/app/_paginator.php` — the place to change the
markup.

## Deployment

`deploy.yaml` is a [Deployer](https://deployer.org) recipe: `storage` and
`public/uploads` are shared between releases, `.env` stays outside the
repository.

```bash
vendor/bin/dep deploy
```

## Development

```bash
php -S localhost:8000 -t public
```

Errors are shown when `displayErrorDetails` in `app/settings.php` is on; leave
it off in production, where the log is written to `storage/logs/motor.log` and
read from `/admin/logs`.

## License

GPL-3.0-only
