<?php

declare(strict_types=1);

use App\Factories\AppFactory;
use App\Models\User;
use App\Services\BBCode;
use App\Services\Session;
use App\Services\Setting;
use App\Services\Str;
use DI\Container;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Exception\HttpException;

/**
 * Escape data
 *
 * @param mixed $value
 * @param bool  $doubleEncode
 *
 * @return array|string
 */
function escape(mixed $value, bool $doubleEncode = true): mixed
{
    $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401;

    if (is_array($value)) {
        foreach ($value as $key => $val) {
            $value[$key] = escape($val, $doubleEncode);
        }
    } else {
        $value = htmlspecialchars((string) $value, $flags, 'UTF-8', $doubleEncode);
    }

    return $value;
}


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
    $checkText = escape($text);

    if (! $parse) {
        return $bbCode->clear($checkText);
    }

    $parseText = $bbCode->parse($checkText);

    return $bbCode->parseStickers($parseText);
}

/**
 * Возвращает обрезанный текст с закрытием тегов
 *
 * @param string $text
 * @param int    $words
 * @param string $end
 *
 * @return string
 */
function bbCodeTruncate(string $text, int $words = 20, string $end = '...'): string
{
    $bbCode = new BBCode();

    $text = Str::words($text, $words, $end);
    $text = bbCode($bbCode->closeTags($text));

    return preg_replace('/\[(.*?)]/', '', $text);
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
    $app = AppFactory::getInstance();

    if ($abstract === null) {
        return $app->getContainer();
    }

    return $app->getContainer()->get($abstract);
}

/**
 * Get route by name
 *
 * @param string $routeName
 * @param array  $data
 * @param array  $queryParams
 *
 * @return string
 */
function route(string $routeName, array $data = [], array $queryParams = []): string
{
    return app(App::class)
        ->getRouteCollector()
        ->getRouteParser()
        ->urlFor($routeName, $data, $queryParams);
}


/**
 * Get session
 *
 * @param string|null $key
 * @param mixed|null  $default
 *
 * @return Session|mixed
 */
function session(?string $key = null, mixed $default = null): mixed
{
    /** @var Session $session */
    $session = app(Session::class);

    if ($key === null) {
        return $session;
    }

    return $session->get($key, $default);
}

/**
 * Get setting
 *
 * @param string|null $key
 * @param mixed|null  $default
 *
 * @return Setting|mixed
 */
function setting(?string $key = null, mixed $default = null): mixed
{
    /** @var Setting $setting */
    $setting = app(Setting::class);

    if ($key === null) {
        return $setting;
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
    $login    = session('login');
    $password = session('password');

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
    return dirname(__DIR__) . '/' . ltrim($path, '/');
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
    return basePath('/public/' . ltrim($path, '/'));
}

/**
 * Get storage path
 *
 * @param string $path
 *
 * @return string
 */
function storagePath(string $path = ''): string
{
    return basePath('/storage/' . ltrim($path, '/'));
}

/**
 * Throw an Exception with the given data.
 *
 * @param int    $code
 * @param string $message
 *
 * @return void
 */
function abort(int $code, string $message = ''): void
{
    $serverRequestCreator = ServerRequestCreatorFactory::create();
    $request = $serverRequestCreator->createServerRequestFromGlobals();

    throw new HttpException($request, $message, $code);
}

/**
 * Session old
 *
 * @param string     $key
 * @param mixed|null $default
 *
 * @return mixed
 */
function old(string $key, mixed $default = null): mixed
{
    if (! session('flash.old')) {
        return escape($default);
    }

    $old = session('flash.old.' . $key, $default);

    return escape($old);
}

/**
 * Session has error
 *
 * @param string $field
 *
 * @return string
 */
function hasError(string $field): string
{
    if (session('flash.errors')) {
        return session('flash.errors.' . $field) ? ' is-invalid' : ' is-valid';
    }

    return '';
}

/**
 * Session get error
 *
 * @param string $field
 *
 * @return string
 */
function getError(string $field): string
{
    return session('flash.errors.' . $field, '');
}
