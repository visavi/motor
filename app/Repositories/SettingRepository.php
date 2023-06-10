<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Setting;

class SettingRepository implements RepositoryInterface
{
    /**
     * Get setting
     *
     * @return array
     */
    public function getSettings(): array
    {
        $settings = Setting::query()
            ->get()
            ->pluck('value', 'name')
            ->all();

        return array_map(static function ($value) {
            if (is_numeric($value)) {
                return ! str_contains($value, '.') ? (int) $value : (float) $value;
            }

            if ($value === '') {
                return null;
            }

            return $value;
        }, $settings);
    }
}
