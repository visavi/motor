<?php

declare(strict_types=1);

namespace App\Services;

class Setting
{
    private array $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get settings
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key = ''): mixed
    {
        return empty($key) ? $this->settings : $this->settings[$key];
    }
}
