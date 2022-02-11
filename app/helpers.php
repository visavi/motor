<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\BBCode;
use App\Services\Session;
use App\Services\Setting;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\ServerRequestCreatorFactory;

/**
 * Sanitize
 *
 * @param string $str
 *
 * @return string
 */
function sanitize(string $str): string
{
    // preg_replace('/\R/u', '', $str);
    $search = ["\0", "\x00", "\x1A", chr(226) . chr(128) . chr(174)];
    $str = str_replace($search, '', $str);

    return trim($str);
}

/**
 * BBCode
 *
 * @param mixed $text
 * @param bool  $parse
 *
 * @return string
 */
function bbCode(mixed $text, bool $parse = true): string
{
    $bbCode = new BBCode();
    $checkText = htmlspecialchars((string) $text);

    if (! $parse) {
        return $bbCode->clear($checkText);
    }

    $parseText = $bbCode->parse($checkText);
    $parseText = $bbCode->parseStickers($parseText);

    return $parseText;
}

/**
 * Возвращает размер в читаемом формате
 *
 * @param int $bytes
 * @param int $precision
 *
 * @return string
 */
function formatSize(int $bytes, int $precision = 2): string
{
    $units = ['B', 'Kb', 'Mb', 'Gb', 'Tb'];
    $pow   = floor(($bytes ? log($bytes) : 0) / log(1000));
    $pow   = min($pow, count($units) - 1);

    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . $units[$pow];
}

/**
 * @param string|null $abstract
 *
 * @return mixed|Container
 */
function app(?string $abstract = null): mixed
{
    $container = require __DIR__ . '/../app/container.php';
    if ($abstract === null) {
        return $container;
    }

    return $container->get($abstract);
}

/**
 * Get session
 *
 * @return Session
 */
function session(): Session
{
    static $session;

    if (! $session) {
        $session = app(Session::class);
    }

    return $session;
}

/**
 * @param string|null $key
 * @param mixed|null  $default
 *
 * @return mixed|string|null
 * @throws \DI\DependencyException
 * @throws \DI\NotFoundException
 */
function settings(?string $key = null, mixed $default = null): mixed
{
    static $setting;

    if (! $setting) {
        $setting = app(Setting::class);
    }

    if ($key === null) {
        return $setting->all();
    }

    return $setting->get($key, $default);
}

/**
 * Check auth
 *
 * @return User|bool
 */
function checkAuth(): User|bool
{
    $login    = session()->get('login');
    $password = session()->get('password');

    if ($login && $password) {
        $user = User::query()->where('login', $login)->first();

        if ($user && $password === $user->password) {
            return $user;
        }
    }

    return false;
}

/**
 * Is user
 *
 * @return bool
 */
function isUser(): bool
{
    return (bool) getUser();
}

/**
 * Get user
 *
 * @param string $key
 *
 * @return User|bool|null
 */
function getUser(string $key = ''): mixed
{
    static $user;

    if (! $user) {
        $user = checkAuth();
    }

    return $key ? ($user->$key ?? null) : $user;
}

/**
 * Is admin
 *
 * @param string $role
 *
 * @return bool
 */
function isAdmin(string $role = User::EDITOR): bool
{
    $group = array_flip(User::ALL_GROUP);

    return isUser()
        && isset($group[$role], $group[getUser('role')])
        && $group[getUser('role')] <= $group[$role];
}

/**
 * Get extension
 *
 * @param string $filename Имя файла
 *
 * @return string расширение
 */
function getExtension(string $filename): string
{
    return pathinfo($filename, PATHINFO_EXTENSION);
}

/**
 * Get unique name
 *
 * @param string|null $extension
 *
 * @return string
 */
function uniqueName(string $extension = null): string
{
    if ($extension) {
        $extension = '.' . $extension;
    }

    return str_replace('.', '', uniqid('', true)) . $extension;
}

/**
 * Get root path
 *
 * @param string $path
 *
 * @return string
 */
function basePath(string $path = ''): string
{
    return dirname(__DIR__) . $path;
}

/**
 * Get public path
 *
 * @param string $path
 *
 * @return string
 */
function publicPath(string $path = ''): string
{
    return basePath('/public' . $path);
}

/**
 * Throw an Exception with the given data.
 *
 * @param int    $code
 * @param string $message
 *
 * @return void
 * @throws \Slim\Exception\HttpException
 */
function abort(int $code, string $message = ''): void
{
    $serverRequestCreator = ServerRequestCreatorFactory::create();
    $request = $serverRequestCreator->createServerRequestFromGlobals();

    throw new \Slim\Exception\HttpException($request, $message, $code);
}


// Old
function old(string $key, mixed $default = null): mixed
{
    if (! isset(session()->get('flash')['old'])) {
        return $default;
    }

    return session()->get('flash')['old'][$key] ?? $default;
}

// HasError
function hasError(string $field): string
{
    if (isset(session()->get('flash')['errors'])) {
        return isset(session()->get('flash')['errors'][$field]) ? ' is-invalid' : ' is-valid';
    }

    return '';
}

// Get Error
function getError(string $field): string
{
    return session()->get('flash')['errors'][$field] ?? '';
}
