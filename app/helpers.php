<?php

use App\Models\User;
use App\Services\BBCode;
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
function session()
{
    return new Session();
}

/**
 * Is user
 *
 * @return bool
 */
function isUser()
{
    $login    = session()->get('login');
    $password = session()->get('password');

    if ($login && $password) {
        $user = User::query()->where('login', $login)->first();

        return  $user && $password === $user->password;
    }

    return false;
}
