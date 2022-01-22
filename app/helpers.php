<?php

use App\Services\BBCode;

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
 * Возвращает размер в человеко читаемом формате
 *
 * @param int $bytes     размер в байтах
 * @param int $precision кол. символов после запятой
 *
 * @return string Форматированный вывод размера
 */
function formatSize(int $bytes, int $precision = 2): string
{
    $units = ['B', 'Kb', 'Mb', 'Gb', 'Tb'];
    $pow   = floor(($bytes ? log($bytes) : 0) / log(1000));
    $pow   = min($pow, count($units) - 1);

    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . $units[$pow];
}
