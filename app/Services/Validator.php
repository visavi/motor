<?php

declare(strict_types=1);

namespace App\Services;

use BadMethodCallException;
use Countable;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class Validation data
 *
 * @license Code and contributions have MIT License
 * @link    https://visavi.net
 * @author  Alexander Grigorev <admin@visavi.net>
 *
 * @method $this required(array|string $key, ?string $label = null)
 * @method $this length(array|string $key, int $min, int $max, ?string $label = null)
 * @method $this minLength(array|string $key, int $length, ?string $label = null)
 * @method $this maxLength(array|string $key, int $length, ?string $label = null)
 * @method $this range(array|string $key, int|float $min, int|float $max, ?string $label = null)
 * @method $this gt(array|string $key, int|float $num, ?string $label = null)
 * @method $this gte(array|string $key, int|float $num, ?string $label = null)
 * @method $this lt(array|string $key, int|float $num, ?string $label = null)
 * @method $this lte(array|string $key, int|float $num, ?string $label = null)
 * @method $this equal(string $key1, string $key2, ?string $label = null)
 * @method $this notEqual(string $key1, string $key2, ?string $label = null)
 * @method $this empty(array|string $key, ?string $label = null)
 * @method $this notEmpty(array|string $key, ?string $label = null)
 * @method $this in(array|string $key, array $haystack, ?string $label = null)
 * @method $this notIn(array|string $key, array $haystack, ?string $label = null)
 * @method $this regex(array|string $key, string $pattern, ?string $label = null)
 * @method $this url(array|string $key, ?string $label = null)
 * @method $this email(array|string $key, ?string $label = null)
 * @method $this ip(array|string $key, ?string $label = null)
 * @method $this phone(array|string $key, ?string $label = null)
 * @method $this file(string $key, array $rules)
 * @method $this add(string $key, callable $callable, string $label)
 *
 */
class Validator
{
    private array $rules;
    private array $errors;
    private array $input;

    private array $data = [
        'required'      => 'Поле %s является обязательным',
        'length'        => [
            'between' => 'Количество символов в поле %s должно быть от %d до %d',
            'min'     => 'Количество символов в поле %s должно быть не меньше %d',
            'max'     => 'Количество символов в поле %s должно быть не больше %d',
        ],
        'range'         => 'Значение поля %s должно быть между %d и %d',
        'gt'            => 'Значение поля %s должно быть больше %d',
        'gte'           => 'Значение поля %s должно быть больше или равно %d',
        'lt'            => 'Значение поля %s должно быть меньше %d',
        'lte'           => 'Значение поля %s должно быть меньше или равно %d',

        'equal'         => 'Значения полей %s и %s должны совпадать',
        'notEqual'      => 'Значения полей %s и %s должны различаться',
        'empty'         => 'Значение поля %s должно быть пустым',
        'notEmpty'      => 'Значение поля %s не должно быть пустым',
        'in'            => 'Значение поля %s ошибочно',
        'notIn'         => 'Значение поля %s ошибочно',
        'regex'         => 'Значение поля %s имеет ошибочный формат',
        'url'           => 'Значение поля %s содержит недействительный URL',
        'email'         => 'Значение поля %s содержит недействительный email',
        'ip'            => 'Значение поля %s содержит недействительный IP-адрес',
        'phone'         => 'Значение поля %s содержит недействительный номер телефона',
        'file'          => [
            'error'        => 'Ошибка загрузки файла',
            'extension'    => 'Поле %s должно быть файлом одного из следующих типов: %s',
            'size_max'     => 'Размер файла в поле %s должен быть не больше %s',
            'weight_min'   => 'Размер изображения в поле %s не должен быть меньше %s px',
            'weight_max'   => 'Размер изображения в поле %s не должен быть больше %s px',
            'weight_empty' => 'Размер изображения в поле %s слишком маленький!',
        ],
    ];

    /**
     * Call
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call(string $name, array $arguments): self
    {
        $keys = (array) $arguments[0];

        foreach ($keys as $key) {
            $arguments[0] = $key;
            $this->rules[$key][$name] = $arguments;
        }

        return $this;
    }

    /**
     * Возвращает успешность валидации
     *
     * @param array $input
     *
     * @return bool
     */
    public function isValid(array $input): bool
    {
        $this->input = $input;

        foreach ($this->rules as $rules) {
            foreach ($rules as $rule => $params) {
                $method = $rule . 'Rule';

                if (! method_exists($this, $method)) {
                    throw new BadMethodCallException(sprintf('%s() called undefined method. Method "%s" does not exist', __METHOD__, $rule));
                }

                $this->$method(...$params);
            }
        }

        return empty($this->errors);
    }

    /**
     * Required
     *
     * @param array|string $key
     * @param string|null  $label
     *
     * @return $this
     */
    private function requiredRule(array|string $key, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if ($this->blank($input)) {
                $this->addError($field, sprintf($label ?? $this->data['required'], $field));
            }
        }

        return $this;
    }

    /**
     * Length
     *
     * @param array|string $key
     * @param int          $min
     * @param int          $max
     * @param string|null  $label
     *
     * @return $this
     */
    private function lengthRule(array|string $key, int $min, int $max, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if (mb_strlen((string) $input, 'utf-8') < $min || mb_strlen((string) $input, 'utf-8') > $max) {
                $this->addError($field, sprintf($label ?? $this->data['length']['between'], $field, $min, $max));
            }
        }

        return $this;
    }

    /**
     * Min length
     *
     * @param array|string $key
     * @param int          $length
     * @param string|null  $label
     *
     * @return $this
     */
    private function minLengthRule(array|string $key, int $length, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if (mb_strlen((string) $input, 'utf-8') < $length) {
                $this->addError($field, sprintf($label ?? $this->data['length']['min'], $field, $length));
            }
        }

        return $this;
    }

    /**
     * Max length
     *
     * @param array|string $key
     * @param int    $length
     * @param string|null  $label
     *
     * @return $this
     */
    private function maxLengthRule(array|string $key, int $length, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if (mb_strlen((string) $input, 'utf-8') > $length) {
                $this->addError($field, sprintf($label ?? $this->data['length']['max'], $field, $length));
            }
        }

        return $this;
    }

    /**
     * Range
     *
     * @param array|string $key
     * @param int|float    $min
     * @param int|float    $max
     * @param string|null  $label
     *
     * @return $this
     */
    private function rangeRule(array|string $key, int|float $min, int|float $max, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if ($input < $min || $input > $max) {
                $this->addError($field, sprintf($label ?? $this->data['range'], $field, $min, $max));
            }
        }

        return $this;
    }

    /**
     * Greater than
     *
     * @param array|string $key
     * @param int|float    $num
     * @param string|null  $label
     *
     * @return $this
     */
    private function gtRule(array|string $key, int|float $num, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if ($input <= $num) {
                $this->addError($field, sprintf($label ?? $this->data['gt'], $field, $num));
            }
        }

        return $this;
    }

    /**
     * Greater than or equal
     *
     * @param array|string $key
     * @param int|float    $num
     * @param string|null  $label
     *
     * @return $this
     */
    private function gteRule(array|string $key, int|float $num, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if ($input < $num) {
                $this->addError($field, sprintf($label ?? $this->data['gte'], $field, $num));
            }
        }

        return $this;
    }

    /**
     * Less than
     *
     * @param array|string $key
     * @param int|float    $num
     * @param string|null  $label
     *
     * @return $this
     */
    private function ltRule(array|string $key, int|float $num, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if ($input >= $num) {
                $this->addError($field, sprintf($label ?? $this->data['lt'], $field, $num));
            }
        }

        return $this;
    }

    /**
     * Less than or equal
     *
     * @param array|string $key
     * @param int|float    $num
     * @param string|null  $label
     *
     * @return $this
     */
    private function lteRule(array|string $key, int|float $num, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if ($input > $num) {
                $this->addError($field, sprintf($label ?? $this->data['lte'], $field, $num));
            }
        }

        return $this;
    }

    /**
     * Equal
     *
     * @param string      $key1
     * @param string      $key2
     * @param string|null $label
     *
     * @return $this
     */
    private function equalRule(string $key1, string $key2, ?string $label = null): self
    {
        $input1 = $this->getInput($key1);
        $input2 = $this->getInput($key2);

        if (! $this->isRequired($key1) && $this->blank($input1)) {
            return $this;
        }

        if ($input1 !== $input2) {
            $this->addError($key1, sprintf($label ?? $this->data['equal'], $key1, $key2));
        }

        return $this;
    }

    /**
     * Not equal
     *
     * @param string      $key1
     * @param string      $key2
     * @param string|null $label
     *
     * @return $this
     */
    private function notEqualRule(string $key1, string $key2, ?string $label = null): self
    {
        $input1 = $this->getInput($key1);
        $input2 = $this->getInput($key2);

        if (! $this->isRequired($key1) && $this->blank($input1)) {
            return $this;
        }

        if ($input1 === $input2) {
            $this->addError($key1, sprintf($label ?? $this->data['notEqual'], $key1, $key2));
        }

        return $this;
    }

    /**
     * Empty
     *
     * @param array|string $key
     * @param string|null  $label
     *
     * @return $this
     */
    private function emptyRule(array|string $key, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if (! $this->blank($input)) {
                $this->addError($field, sprintf($label ?? $this->data['empty'], $field));
            }
        }

        return $this;
    }

    /**
     * Not empty
     *
     * @param array|string $key
     * @param string|null  $label
     *
     * @return $this
     */
    private function notEmptyRule(array|string $key, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if ($this->blank($input)) {
                $this->addError($field, sprintf($label ?? $this->data['notEmpty'], $field));
            }
        }

        return $this;
    }

    /**
     * In
     *
     * @param array|string $key
     * @param array        $haystack
     * @param string|null  $label
     *
     * @return $this
     */
    private function inRule(array|string $key, array $haystack, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if (! in_array($input, $haystack, true)) {
                $this->addError($field, sprintf($label ?? $this->data['in'], $field));
            }
        }

        return $this;
    }

    /**
     * Not in
     *
     * @param array|string $key
     * @param array        $haystack
     * @param string|null  $label
     *
     * @return $this
     */
    private function notInRule(array|string $key, array $haystack, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if (in_array($input, $haystack, true)) {
                $this->addError($field, sprintf($label ?? $this->data['notIn'], $field));
            }
        }

        return $this;
    }

    /**
     * Regex
     *
     * @param array|string $key
     * @param string       $pattern
     * @param string|null  $label
     *
     * @return $this
     */
    private function regexRule(array|string $key, string $pattern, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if (! preg_match($pattern, $input)) {
                $this->addError($field, sprintf($label ?? $this->data['regex'], $field));
            }
        }

        return $this;
    }

    /**
     * Check url
     *
     * @param array|string $key
     * @param string|null  $label
     *
     * @return $this
     */
    private function urlRule(array|string $key, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if (! preg_match('|^https?://([а-яa-z0-9_\-.])+(\.([а-яa-z0-9/\-?_=#])+)+$|iu', $input)) {
                $this->addError($field, sprintf($label ?? $this->data['url'], $field));
            }
        }

        return $this;
    }

    /**
     * Check email
     *
     * @param array|string $key
     * @param string|null  $label
     *
     * @return $this
     */
    private function emailRule(array|string $key, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if (filter_var($input, FILTER_VALIDATE_EMAIL) === false) {
                $this->addError($field, sprintf($label ?? $this->data['email'], $field));
            }
        }

        return $this;
    }

    /**
     * Check IP address
     *
     * @param array|string $key
     * @param string|null  $label
     *
     * @return $this
     */
    private function ipRule(array|string $key, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if (filter_var($input, FILTER_VALIDATE_IP) === false) {
                $this->addError($field, sprintf($label ?? $this->data['ip'], $field));
            }
        }

        return $this;
    }

    /**
     * Check phone
     *
     * @param array|string $key
     * @param string|null  $label
     *
     * @return $this
     */
    private function phoneRule(array|string $key, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            $input = $this->getInput($field);

            if (! $this->isRequired($field) && $this->blank($input)) {
                return $this;
            }

            if (! preg_match('#^\d{8,13}$#', $input)) {
                $this->addError($field, sprintf($label ?? $this->data['phone'], $field));
            }
        }

        return $this;
    }

    /**
     * Custom rule
     *
     * @param string   $key
     * @param callable $callable
     * @param string   $label
     *
     * @return $this
     */
    private function addRule(string $key, callable $callable, string $label)
    {
        $input = $this->getInput($key);

        if (! $callable($input)) {
            $this->addError($key, $label);
        }

        return $this;
    }

    /**
     * Проверяет файл
     *
     * @param string $key
     * @param array  $rules
     *
     * @return $this
     */
    private function fileRule(string $key, array $rules): self
    {
        $input = $this->getInput($key);

        if (! $this->isRequired($key) && $this->blank($input)) {
            return $this;
        }

        if (! $input instanceof UploadedFileInterface) {
            $this->addError($key, sprintf($this->data['file']['error'], $key));
            return $this;
        }

        if ($input->getError() !== UPLOAD_ERR_OK) {
            $this->addError($key, $this->getUploadErrorByCode($input->getError()));
            return $this;
        }

        if (empty($rules['extensions'])) {
            $rules['extensions'] = ['jpg', 'jpeg', 'gif', 'png'];
        }

        $extension = strtolower(pathinfo($input->getClientFilename(), PATHINFO_EXTENSION));
        if (! in_array($extension, $rules['extensions'], true)) {
            $this->addError($key, sprintf($this->data['file']['extension'], $key, implode(', ', $rules['extensions'])));
        }

        if (isset($rules['size_max']) && $input->getSize() > $rules['size_max']) {
            $this->addError($key, sprintf($this->data['file']['size_max'], $key, formatSize($rules['size_max'])));
        }

        if (in_array($extension, ['jpg', 'jpeg', 'gif', 'png'], true)) {
            [$width, $height] = getimagesize($input->getFilePath());

            if (isset($rules['weight_max'])) {
                if ($width > $rules['weight_max'] || $height > $rules['weight_max']) {
                    $this->addError($key, sprintf($this->data['file']['weight_max'], $key, $rules['weight_max']));
                }
            }

            if (isset($rules['weight_min'])) {
                if ($width < $rules['weight_min'] || $height < $rules['weight_min']) {
                    $this->addError($key, sprintf($this->data['file']['weight_min'], $key, $rules['weight_min']));
                }
            } elseif (empty($width) || empty($height)) {
                $this->addError($key, sprintf($this->data['file']['weight_empty'], $key));
            }
        }

        return $this;
    }

    /**
     * Add error
     *
     * @param string $key   Field name
     * @param string $label Text error
     *
     * @return void
     */
    public function addError(string $key, string $label): void
    {
        if (isset($this->errors[$key])) {
            $this->errors[] = $label;
        } else {
            $this->errors[$key] = $label;
        }
    }

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Determine if the given value is "blank".
     *
     * @param  mixed  $value
     * @return bool
     */
    private function blank(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        if ($value instanceof UploadedFileInterface) {
            return $value->getError() === UPLOAD_ERR_NO_FILE;
        }

        return empty($value);
    }

    /**
     * Is required
     *
     * @param string $key
     * @return bool
     */
    private function isRequired(string $key): bool
    {
        return isset($this->rules[$key]['required']);
    }

    /**
     * Get input
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    private function getInput(string $key, mixed $default = null): mixed
    {
        return $this->input[$key] ?? $default;
    }

    /**
     * Get upload error by code
     *
     * @param int $code
     *
     * @return string
     */
    private function getUploadErrorByCode(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE   => 'Размер файла превысил значение upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE  => 'Размер файла превысил значение MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL    => 'Загруженный файл был загружен только частично',
            UPLOAD_ERR_NO_FILE    => 'Файл не был загружен',
            UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка',
            UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск',
            UPLOAD_ERR_EXTENSION  => 'Модуль PHP остановил загрузку файла',
            default               => 'Неизвестная ошибка загрузки',
        };
    }
}
