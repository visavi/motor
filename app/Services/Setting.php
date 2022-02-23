<?php

declare(strict_types=1);

namespace App\Services;

class Setting
{
    private array $setting;

    public function __construct(array $setting)
    {
        $this->setting = $setting;
    }

    /**
     * Get setting by key
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = $this->setting;

        if (! str_contains($key, '.')) {
            return $setting[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($setting) && array_key_exists($segment, $setting)) {
                $setting = $setting[$segment];
            } else {
                return $default;
            }
        }

        return $setting;
    }
}
