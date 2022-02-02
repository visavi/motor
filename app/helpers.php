<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\BBCode;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\ServerRequestCreatorFactory;
use SlimSession\Helper as Session;

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
    //$parseText = $bbCode->parseStickers($parseText);

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
 * Get session
 *
 * @return Session
 */
function session(): Session
{
    static $session;

    if (! $session) {
        $session = new Session();
    }

    return $session;
}

/**
 * Is user
 *
 * @return bool
 */
function isUser(): bool
{
    $login    = session()->get('login');
    $password = session()->get('password');

    if ($login && $password) {
        $user = User::query()->where('login', $login)->first();

        return  $user && $password === $user->password;
    }

    return false;
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
 * Get public path
 *
 * @param string $path
 *
 * @return string
 */
function publicPath(string $path = ''): string
{
    return dirname(__DIR__) . '/public' . $path;
}

/**
 * Throw an Exception with the given data.
 *
 * @param int    $code
 * @param string $message
 *
 * @return never
 * @throws RuntimeException|\Slim\Exception\HttpException
 */
function abort(int $code, string $message = ''): never
{
    $serverRequestCreator = ServerRequestCreatorFactory::create();
    $request = $serverRequestCreator->createServerRequestFromGlobals();

    throw new \Slim\Exception\HttpException($request, $message, $code);
}


// Old
function old(string $key, mixed $default = null)
{
    if (! isset(session()['flash']['old'])) {
        return $default;
    }

    return session()['flash']['old'][$key] ?? $default;
}

// HasError
function hasError(string $field)
{
    if (isset(session()['flash']['errors'])) {
        return isset(session()['flash']['errors'][$field]) ? ' is-invalid' : ' is-valid';
    }

    return '';
}

// Get Error
function getError(string $field)
{
    return session()['flash']['errors'][$field] ?? null;
}

function view(ResponseInterface $response, string $template, array $data = [])
{
    $view = new League\Plates\Engine(dirname(__DIR__) . '/resources/views');

    $render = $view->render($template, $data);

    $response->getBody()->write($render);

    return $response;
}
