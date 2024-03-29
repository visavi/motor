<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Sticker;
use App\Models\User;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Класс обработки BB-кодов
 *
 * @license Code and contributions have MIT License
 * @link    https://visavi.net
 * @author  Alexander Grigorev <admin@visavi.net>
 */
class BBCode
{
    /**
     * @var array
     */
    protected static $parsers = [
        'code' => [
            'pattern'  => '/\[code\](.+?)\[\/code\]/s',
            'callback' => 'highlightCode'
        ],
        'http' => [
            'pattern'  => '%\b(((?<=^|\s)\w+://)[^\s()<>\[\]]+)%s',
            'callback' => 'urlReplace',
        ],
        'link' => [
            'pattern'  => '%\[url\]((\w+://|//|/)[^\s()<>\[\]]+)\[/url\]%s',
            'callback' => 'urlReplace',
        ],
        'namedLink' => [
            'pattern'  => '%\[url\=((\w+://|//|/)[^\s()<>\[\]]+)\](.+?)\[/url\]%s',
            'callback' => 'urlReplace',
        ],
        'image' => [
            'pattern' => '%\[img\]((\w+://|//|/)[^\s()<>\[\]]+\.(jpg|jpeg|png|gif|bmp|webp))\[/img\]%s',
            'replace' => '<div class="media-file"><a href="$1" data-fancybox="gallery"><img src="$1" class="img-fluid" alt="image"></a></div>',
        ],
        'bold' => [
            'pattern' => '/\[b\](.+?)\[\/b\]/s',
            'replace' => '<strong>$1</strong>',
        ],
        'italic' => [
            'pattern' => '/\[i\](.+?)\[\/i\]/s',
            'replace' => '<em>$1</em>',
        ],
        'underLine' => [
            'pattern' => '/\[u\](.+?)\[\/u\]/s',
            'replace' => '<u>$1</u>',
        ],
        'lineThrough' => [
            'pattern' => '/\[s\](.+?)\[\/s\]/s',
            'replace' => '<del>$1</del>',
        ],
        'fontSize' => [
            'pattern'  => '/\[size\=([1-5])\](.+?)\[\/size\]/s',
            'callback' => 'fontSize',
        ],
        'fontColor' => [
            'pattern' => '/\[color\=(#[A-f0-9]{6}|#[A-f0-9]{3})\](.+?)\[\/color\]/s',
            'replace' => '<span style="color:$1">$2</span>',
            'iterate' => 5,
        ],
        'center' => [
            'pattern' => '/\[center\](.+?)\[\/center\]/s',
            'replace' => '<div style="text-align:center;">$1</div>',
        ],
        'quote' => [
            'pattern' => '/\[quote\](.+?)\[\/quote\]/s',
            'replace' => '<blockquote class="blockquote">$1</blockquote>',
            'iterate' => 3,
        ],
        'namedQuote' => [
            'pattern' => '/\[quote\=(.+?)\](.+?)\[\/quote\]/s',
            'replace' => '<blockquote class="blockquote">$2<footer class="blockquote-footer">$1</footer></blockquote>',
            'iterate' => 3,
        ],
        'orderedList' => [
            'pattern'  => '/\[list=1\](.+?)\[\/list\]/s',
            'callback' => 'listReplace',
        ],
        'unorderedList' => [
            'pattern'  => '/\[list\](.+?)\[\/list\]/s',
            'callback' => 'listReplace',
        ],
        'spoiler' => [
            'pattern'  => '/\[spoiler\](.+?)\[\/spoiler\]/s',
            'callback' => 'spoilerText',
            'iterate'  => 1,
        ],
        'shortSpoiler' => [
            'pattern'  => '/\[spoiler\=(.+?)\](.+?)\[\/spoiler\]/s',
            'callback' => 'spoilerText',
            'iterate'  => 1,
        ],
        'hide' => [
            'pattern'  => '/\[hide\](.+?)\[\/hide\]/s',
            'callback' => 'hiddenText',
        ],
        'youtube' => [
            'pattern' => '/\[youtube\](.*youtu(?:\.be\/|be\.com\/.*(?:vi?\/?=?|embed\/)))([\w-]{11}).*\[\/youtube\]/U',
            'replace' => '<div class="media-file ratio ratio-16x9"><iframe src="//www.youtube.com/embed/$2" allowfullscreen></iframe></div>',
        ],
        'username' => [
            'pattern'  => '/(?<=^|\s)@([\w\-]{3,20}+)(?=(\s|,))/',
            'callback' => 'userReplace',
        ],
    ];

    /**
     * BBCode
     *
     * @param mixed $text
     * @param bool  $parse
     *
     * @return string
     */
    public function handle(mixed $text, bool $parse = true): string
    {
        $checkText = escape($text);

        if (! $parse) {
            return $this->clear($checkText);
        }

        $parseText = $this->parse($checkText);

        return $this->parseStickers($parseText);
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
    public function truncate(string $text, int $words = 20, string $end = '...'): string
    {
        $text = Str::words($text, $words, $end);
        $text = $this->handle($this->closeTags($text));

        return preg_replace('/\[(.*?)]/', '', $text);
    }

    /**
     * Обрабатывает текст
     *
     * @param string $source текст содержаший BBCode
     *
     * @return string Распарсенный текст
     */
    public function parse(string $source): string
    {
        $source = nl2br($source, false);
        $source = str_replace('[cut]', '', $source);

        foreach (self::$parsers as $parser) {
            $iterate = $parser['iterate'] ?? 1;

            for ($i = 0; $i < $iterate; $i++) {
                if (isset($parser['callback'])) {
                    $source = preg_replace_callback($parser['pattern'], [$this, $parser['callback']], $source);
                } else {
                    $source = preg_replace($parser['pattern'], $parser['replace'], $source);
                }
            }
        }
        return $this->clearBreakLines($source);
    }

    /**
     * Clear break lines
     *
     * @param string $source
     *
     * @return string
     */
    private function clearBreakLines(string $source): string
    {
        $tags = [
            '</div><br>'        => '</div>',
            '</pre><br>'        => '</pre>',
            '</blockquote><br>' => '</blockquote>',
        ];

        return strtr($source, $tags);
    }

    /**
     * Очищает текст от BB-кодов
     *
     * @param string $source Неочищенный текст
     *
     * @return string Очищенный текст
     */
    public function clear(string $source): string
    {
        return preg_replace('/\[(.*?)]/', '', $source);
    }

    /**
     * Обрабатывает ссылки
     *
     * @param array $match ссылка
     *
     * @return string Обработанная ссылка
     */
    public function urlReplace(array $match): string
    {
        $name = $match[3] ?? $match[1];

        $target = '';
        if ($match[2] !== '/') {
            /*if (strpos($match[1], siteDomain(config('app.url'))) !== false) {
                $match[1] = '//' . str_replace($match[2], '', $match[1]);
            } else {*/
                $target = ' target="_blank" rel="nofollow"';
            //}
        }

        return '<a href="' . $match[1] . '"' . $target . '>' . rawurldecode($name) . '</a>';
    }

    /**
     * Обрабатывет списки
     *
     * @param array $match список
     *
     * @return string Обработанный список
     */
    public function listReplace(array $match): string
    {
        $li = preg_split('/<br>\R/', $match[1], -1, PREG_SPLIT_NO_EMPTY);

        if (empty($li)) {
            return $match[0];
        }

        $list = [];
        foreach ($li as $l) {
            $list[] = '<li>' . $l . '</li>';
        }

        $tag  = ! str_contains($match[0], '[list]') ? 'ol' : 'ul';

        return '<' . $tag . '>' . implode($list) . '</' . $tag . '>';
    }

    /**
     * Обрабатывает размер текста
     *
     * @param array $match Массив элементов
     *
     * @return string Обработанный текст
     */
    public function fontSize(array $match): string
    {
        $sizes = [1 => 'x-small', 2 => 'small', 3 => 'medium', 4 => 'large', 5 => 'x-large'];

        return '<span style="font-size:' . $sizes[$match[1]] . '">' . $match[2] . '</span>';
    }

    /**
     * Подсвечивает код
     *
     * @param array $match Массив элементов
     *
     * @return string Текст с подсветкой
     */
    public function highlightCode(array $match): string
    {
        //Чтобы bb-код, стикеры и логины не работали внутри тега [code]
        $match[1] = strtr($match[1], [':' => '&#58;', '[' => '&#91;', '@' => '&#64;', '<br>' => '']);

        return '<pre class="prettyprint">' . $match[1] . '</pre>';
    }

    /**
     * Скрывает текст под спойлер
     *
     * @param array $match массив элементов
     *
     * @return string код спойлера
     */
    public function spoilerText(array $match): string
    {
        $title = empty($match[2]) ? 'Спойлер' : $match[1];
        $text  = empty($match[2]) ? $match[1] : $match[2];

        return '<div class="spoiler">
                <b class="spoiler-title">' . $title . '</b>
                <div class="spoiler-text" style="display: none;">' . $text . '</div>
            </div>';
    }

    /**
     * Скрывает текст от неавторизованных пользователей
     *
     * @param array $match массив элементов
     *
     * @return string скрытый код
     */
    public function hiddenText(array $match): string
    {
        return '<div class="hidden-text">
                <span class="fw-bold">Скрытый текст:</span> ' .
            (getUser() ? $match[1] : 'Авторизуйтесь для просмотра текста') .
            '</div>';
    }

    /**
     * Обрабатывает логины пользователей
     *
     * @param array $match
     *
     * @return string
     */
    public function userReplace(array $match): string
    {
        static $users;

        if (empty($users)) {
            $cache = app(CacheInterface::class);

            $users = $cache->get('users', function (): array {
                return User::query()->get()->pluck('name', 'login')->all();
            });
        }

        if (! array_key_exists($match[1], $users)) {
            return $match[0];
        }

        $name = $users[$match[1]] ?: $match[1];

        return '<a href="/users/' . $match[1] . '">' . escape($name) . '</a>';
    }

    /**
     * Обрабатывет стикеры
     *
     * @param string $source
     *
     * @return string Обработанный текст
     */
    public function parseStickers(string $source): string
    {
        static $stickers;

        if (empty($stickers)) {
            $stickers = Sticker::query()->get()->pluck('path', 'code')->all();
            array_walk($stickers, static fn (&$a, $b) => $a = '<img src="' . $a . '" alt="' . $b . '">');
        }

        return strtr($source, $stickers);
    }

    /**
     * Добавляет или переопределяет парсер
     *
     * @param string $name    Parser name
     * @param string $pattern Pattern
     * @param string $replace Replace pattern
     *
     * @return void
     */
    public function setParser(string $name, string $pattern, string $replace): void
    {
        self::$parsers[$name] = [
            'pattern' => $pattern,
            'replace' => $replace
        ];
    }

    /**
     * Устанавливает список доступных парсеров
     *
     * @param mixed $only parsers
     *
     * @return BBCode object
     */
    public function only(mixed $only = null): self
    {
        $only = is_array($only) ? $only : func_get_args();
        self::$parsers = $this->arrayOnly($only);

        return $this;
    }

    /**
     * Исключает парсеры из набора
     *
     * @param mixed $except parsers
     *
     * @return BBCode object
     */
    public function except(mixed $except = null): self
    {
        $except = is_array($except) ? $except : func_get_args();
        self::$parsers = $this->arrayExcept($except);

        return $this;
    }

    /**
     * Возвращает список всех парсеров
     *
     * @return array array of parsers
     */
    public function getParsers(): array
    {
        return self::$parsers;
    }

    /**
     * Закрывает bb-теги
     *
     * @param string $text
     *
     * @return string
     */
    public function closeTags(string $text): string
    {
        preg_match_all('#\[([a-z]+)(?:=.*)?(?<![/])]#iU', $text, $result);
        $openTags = $result[1];

        preg_match_all('#\[/([a-z]+)]#iU', $text, $result);
        $closedTags = $result[1];

        if ($openTags === $closedTags) {
            return $text;
        }

        $diff = array_diff_assoc($openTags, $closedTags);
        $tags = array_reverse($diff);

        foreach ($tags as $value) {
            $text .= '[/' . $value . ']';
        }

        return $text;
    }

    /**
     * Filters all parsers that you don´t want
     *
     * @param array $only chosen parsers
     *
     * @return array parsers
     */
    private function arrayOnly(array $only): array
    {
        return array_intersect_key(self::$parsers, array_flip($only));
    }

    /**
     * Removes the parsers that you don´t want
     *
     * @param array $excepts
     *
     * @return array parsers
     */
    private function arrayExcept(array $excepts): array
    {
        return array_diff_key(self::$parsers, array_flip($excepts));
    }
}
