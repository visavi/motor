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
 * Сохраняет flash уведомления
 *
 * @param string $status  Статус уведомления
 * @param mixed  $message Массив или текст с уведомлениями
 *
 * @return void
 *
 * @deprecated since 10.1 - Use redirect->with('success', 'Message') or session()->flash()
 */
function setFlash(string $status, $message)
{
    session()->put('flash.' . $status, $message);
}

/**
 * Сохраняет POST данные введенных пользователем
 *
 * @param array $data Массив полей
 *
 * @deprecated since 10.1
 */
function setInput(array $data)
{
    session()->flash('input', json_encode($data));
}

/**
 * Возвращает значение из POST данных
 *
 * @param string $key     Имя поля
 * @param mixed  $default
 *
 * @return mixed Сохраненное значение
 *
 * @deprecated since 10.1 - Use old('field', 'default');
 */
function getInput(string $key, $default = null)
{
    if (session()->missing('input')) {
        return $default;
    }

    $input = json_decode(session()->get('input', []), true);

    return Arr::get($input, $key, $default);
}

/**
 * Подсвечивает блок с полем для ввода сообщения
 *
 * @param string $field Имя поля
 *
 * @return string CSS класс ошибки
 */
function hasError(string $field): string
{
    // Новая валидация
    if (session()->has('errors')) {
        /** @var ViewErrorBag $errors */
        $errors = session()->get('errors');

        return $errors->has($field) ? ' is-invalid' : ' is-valid';
    }

    $isValid = session()->has('flash.danger') ? ' is-valid' : '';

    return session()->has('flash.danger.' . $field) ? ' is-invalid' : $isValid;
}

/**
 * Возвращает блок с текстом ошибки
 *
 * @param string $field Имя поля
 *
 * @return string|null Блоки ошибки
 */
function textError(string $field): ?string
{
    // Новая валидация
    if (session()->has('errors')) {
        /** @var ViewErrorBag $errors */
        $errors = session()->get('errors');

        return $errors->first($field);
    }

    return session()->get('flash.danger.' . $field);
}
