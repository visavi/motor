<?php

declare(strict_types=1);

namespace App\Services;

use BadMethodCallException;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Http\UploadedFile;

/**
 * Class Validation data
 *
 * @license Code and contributions have MIT License
 * @link    https://visavi.net
 * @author  Alexander Grigorev <admin@visavi.net>
 *
 * @method $this required(array|string $key, ?string $label = null)
 * @method $this lengthBetween(array|string $key, int $min, int $max, ?string $label = null)
 * @method $this minLength(array|string $key, int $length, ?string $label = null)
 * @method $this maxLength(array|string $key, int $length, ?string $label = null)
 * @method $this range(array|string $key, int $min, $max, ?string $label = null)
 */
class Validator
{
    private array $rules;
    private array $errors;

    private array $data = [
        'required'      => 'Поле %s является обязательным',
        'lengthBetween' => 'Количество символов в поле %s должно быть от %d до %d',
        'minLength'     => 'Количество символов в поле %s должно быть не меньше %d',
        'maxLength'     => 'Количество символов в поле %s должно быть не больше %d',
        'range'         => 'Значение поля %s должно быть между %d и %d',
    ];

    /**
     * Call
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call(string $name, array $arguments)
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
     * @param array $data
     *
     * @return bool
     */
    public function isValid(array $data): bool
    {
        foreach ($this->rules as $rules) {
            foreach ($rules as $rule => $params) {
                $input = $data[$params[0]] ?? null;
                $method = $rule . 'Rule';

                if (! method_exists($this, $method)) {
                    throw new BadMethodCallException(sprintf('%s() called undefined method. Method "%s" does not exist', __METHOD__, $rule));
                }

                $this->$method($input, ...$params);
            }
        }

        return empty($this->errors);
    }

    /**
     * Required
     *
     * @param mixed        $input
     * @param array|string $key
     * @param string|null  $label
     *
     * @return $this
     */
    private function requiredRule(mixed $input, array|string $key, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            if ($this->blank($input)) {
                $this->addError($field, sprintf($label ?? $this->data['required'], $field));
            }
        }

        return $this;
    }

    /**
     * Length
     *
     * @param mixed        $input
     * @param array|string $key
     * @param int          $min
     * @param int          $max
     * @param string|null  $label
     *
     * @return $this
     */
    private function lengthBetweenRule(mixed $input, array|string $key, int $min, int $max, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            if (! isset($this->rules[$field]['required']) && $this->blank($input)) {
                return $this;
            }

            if (mb_strlen((string) $input, 'utf-8') < $min || mb_strlen((string) $input, 'utf-8') > $max) {
                $this->addError($field, sprintf($label ?? $this->data['lengthBetween'], $field, $min, $max));
            }
        }

        return $this;
    }

    /**
     * Min length
     *
     * @param mixed       $input
     * @param array|string      $key
     * @param int         $length
     * @param string|null $label
     *
     * @return $this
     */
    private function minLengthRule(mixed $input, array|string $key, int $length, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            if (! isset($this->rules[$field]['required']) && $this->blank($input)) {
                return $this;
            }

            if (mb_strlen((string) $input, 'utf-8') < $length) {
                $this->addError($field, sprintf($label ?? $this->data['minLength'], $field, $length));
            }
        }

        return $this;
    }

    /**
     * Max length
     *
     * @param mixed  $input
     * @param array|string $key
     * @param int    $length
     * @param string|null  $label
     *
     * @return $this
     */
    private function maxLengthRule(mixed $input, array|string $key, int $length, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            if (! isset($this->rules[$field]['required']) && $this->blank($input)) {
                return $this;
            }

            if (mb_strlen((string) $input, 'utf-8') > $length) {
                $this->addError($field, sprintf($label ?? $this->data['maxLength'], $field, $length));
            }
        }

        return $this;
    }

    /**
     * Проверяет число на вхождение в диапазон
     *
     * @param mixed        $input
     * @param array|string $key
     * @param int|float    $min
     * @param int|float    $max
     * @param string|null  $label
     *
     * @return $this
     */
    public function rangeRule(mixed $input, array|string $key, int|float $min, int|float $max, ?string $label = null): self
    {
        $key = (array) $key;

        foreach ($key as $field) {
            if (! isset($this->rules[$field]['required']) && $this->blank($input)) {
                return $this;
            }

            if ($input < $min || $input > $max) {
                $this->addError($field, sprintf($label ?? $this->data['range'], $field, $min, $max));
            }
        }

        return $this;
    }

    /**
     * Проверяет на больше чем число
     *
     * @param int|float $input
     * @param int|float $input2
     * @param mixed     $label
     *
     * @return Validator
     */
    public function gt($input, $input2, $label): Validator
    {
        if ($input <= $input2) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет на больше чем или равно
     *
     * @param int|float $input
     * @param int|float $input2
     * @param mixed     $label
     *
     * @return Validator
     */
    public function gte($input, $input2, $label): Validator
    {
        if ($input < $input2) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет на меньше чем число
     *
     * @param int|float $input
     * @param int|float $input2
     * @param mixed     $label
     *
     * @return Validator
     */
    public function lt($input, $input2, $label): Validator
    {
        if ($input >= $input2) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет на меньше чем или равно
     *
     * @param int|float $input
     * @param int|float $input2
     * @param mixed     $label
     *
     * @return Validator
     */
    public function lte($input, $input2, $label): Validator
    {
        if ($input > $input2) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет эквивалентны ли данные
     *
     * @param mixed $input
     * @param mixed $input2
     * @param mixed $label
     *
     * @return Validator
     */
    public function equal($input, $input2, $label): Validator
    {
        if ($input !== $input2) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет не эквивалентны ли данные
     *
     * @param mixed $input
     * @param mixed $input2
     * @param mixed $label
     *
     * @return Validator
     */
    public function notEqual($input, $input2, $label): Validator
    {
        if ($input === $input2) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет пустые ли данные
     *
     * @param mixed $input
     * @param mixed $label
     *
     * @return Validator
     */
    public function empty($input, $label): Validator
    {
        if (! empty($input)) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет не пустые ли данные
     *
     * @param mixed $input
     * @param mixed $label
     *
     * @return Validator
     */
    public function notEmpty($input, $label): Validator
    {
        if (empty($input)) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет на true
     *
     * @param mixed $input
     * @param mixed $label
     *
     * @return Validator
     */
    public function true($input, $label): Validator
    {
        if (filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === false) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет на false
     *
     * @param mixed $input
     * @param mixed $label
     *
     * @return Validator
     */
    public function false($input, $label): Validator
    {
        if (filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== false) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет на вхождение в массив
     *
     * @param mixed $input
     * @param array $haystack
     * @param mixed $label
     *
     * @return Validator
     */
    public function in($input, array $haystack, $label): Validator
    {
        if (! is_array($haystack) || ! in_array($input, $haystack, true)) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет на не вхождение в массив
     *
     * @param mixed $input
     * @param array $haystack
     * @param mixed $label
     *
     * @return Validator
     */
    public function notIn($input, array $haystack, $label): Validator
    {
        if (! is_array($haystack) || in_array($input, $haystack, true)) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет по регулярному выражению
     *
     * @param mixed  $input
     * @param string $pattern
     * @param mixed  $label
     * @param bool   $required
     *
     * @return Validator
     */
    public function regex($input, string $pattern, $label, bool $required = true): Validator
    {
        if (! $required && $this->blank($input)) {
            return $this;
        }

        if (! preg_match($pattern, $input)) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Check float
     *
     * @param mixed $input
     * @param mixed $label
     * @param bool  $required
     *
     * @return Validator
     */
    public function float($input, $label, bool $required = true): Validator
    {
        if (! $required && $this->blank($input)) {
            return $this;
        }

        if (! is_float($input)) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет адрес сайта
     *
     * @param mixed $input
     * @param mixed $label
     * @param bool  $required
     *
     * @return Validator
     */
    public function url($input, $label, bool $required = true): Validator
    {
        if (! $required && $this->blank($input)) {
            return $this;
        }

        if (! preg_match('|^https?://([а-яa-z0-9_\-\.])+(\.([а-яa-z0-9\/\-?_=#])+)+$|iu', $input)) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет email
     *
     * @param mixed $input
     * @param mixed $label
     * @param bool  $required
     *
     * @return Validator
     */
    public function email($input, $label, bool $required = true): Validator
    {
        if (! $required && $this->blank($input)) {
            return $this;
        }

        $validator = new EmailValidator();
        $checkEmail = $validator->isValid($input, new RFCValidation());

        if (! $checkEmail || filter_var($input, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Check IP address
     *
     * @param mixed $input
     * @param mixed $label
     * @param bool  $required
     *
     * @return Validator
     */
    public function ip($input, $label, bool $required = true): Validator
    {
        if (! $required && $this->blank($input)) {
            return $this;
        }

        if (filter_var($input, FILTER_VALIDATE_IP) === false) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Check phone
     *
     * @param mixed $input
     * @param mixed $label
     * @param bool  $required
     *
     * @return Validator
     */
    public function phone($input, $label, bool $required = true): Validator
    {
        if (! $required && $this->blank($input)) {
            return $this;
        }

        if (! preg_match('#^\d{8,13}$#', $input)) {
            $this->addError($label);
        }

        return $this;
    }

    /**
     * Проверяет файл
     *
     * @param UploadedFile|null $input
     * @param array             $rules
     * @param mixed             $label
     * @param bool              $required
     *
     * @return Validator
     */
    public function file(?UploadedFile $input, array $rules, $label, bool $required = true): Validator
    {
        if (! $required && $this->blank($input)) {
            return $this;
        }

        if (! $input instanceof UploadedFile) {
            $this->addError($label);
            return $this;
        }

        if (! $input->isValid()) {
            $this->addError($input->getErrorMessage());
            return $this;
        }

        $key = is_array($label) ? key($label) : 0;

        if (empty($rules['extensions'])) {
            $rules['extensions'] = ['jpg', 'jpeg', 'gif', 'png'];
        }

        $extension = strtolower($input->getClientOriginalExtension());

        if (! in_array($extension, $rules['extensions'], true)) {
            $this->addError([$key => __('validator.extension')]);
        }

        if (isset($rules['maxsize']) && $input->getSize() > $rules['maxsize']) {
            $this->addError([$key => __('validator.size_max', ['size' => formatSize($rules['maxsize'])])]);
        }

        if (in_array($extension, ['jpg', 'jpeg', 'gif', 'png'], true)) {
            [$width, $height] = getimagesize($input->getPathname());

            if (isset($rules['maxweight'])) {
                if ($width > $rules['maxweight'] || $height > $rules['maxweight']) {
                    $this->addError([$key => __('validator.weight_max', ['weight' => $rules['maxweight']])]);
                }
            }

            if (isset($rules['minweight'])) {
                if ($width < $rules['minweight'] || $height < $rules['minweight']) {
                    $this->addError([$key => __('validator.weight_min', ['weight' => $rules['minweight']])]);
                }
            } elseif (empty($width) || empty($height)) {
                $this->addError([$key => __('validator.weight_empty')]);
            }
        }

        return $this;
    }

    /**
     * Добавляет ошибки в массив
     *
     * @param string      $key
     * @param string|null $label Текст ошибки
     *
     * @return void
     */
    public function addError(string $key, ?string $label = null): void
    {
        if (isset($this->errors[$key])) {
            $this->errors[] = $label;
        } else {
            $this->errors[$key] = $label;
        }
    }

    /**
     * Возвращает список ошибок
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Очищает список ошибок
     *
     * @return void
     */
    public function clearErrors(): void
    {
        $this->errors = [];
    }

    /**
     * Determine if the given value is "blank".
     *
     * @param  mixed  $value
     * @return bool
     */
    public function blank(mixed $value): bool
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

        if ($value instanceof \Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }
}
