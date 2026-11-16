<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;
use ZipArchive;

/**
 * Резервные копии таблиц
 */
class BackupService
{
    /**
     * Упаковывает storage/database в архив в storage/backups
     *
     * @return string имя архива
     *
     * @throws RuntimeException
     */
    public function create(): string
    {
        $databaseDir = storagePath('database/');
        $name        = $this->freeName('backup_' . date('Y_m_d_His'));

        $zip    = new ZipArchive();
        $result = $zip->open(storagePath('backups/' . $name), ZipArchive::CREATE);

        if ($result !== true) {
            throw new RuntimeException('Backup ' . $name . ' was not created, zip error ' . $result);
        }

        foreach (scandir($databaseDir) ?: [] as $file) {
            if (str_starts_with($file, '.') || ! is_file($databaseDir . $file)) {
                continue;
            }

            $zip->addFile($databaseDir . $file, $file);
        }

        $zip->close();

        return $name;
    }

    /**
     * Имя архива, которого ещё нет
     *
     * @param string $base
     *
     * @return string
     */
    private function freeName(string $base): string
    {
        $name = $base . '.zip';

        for ($i = 2; file_exists(storagePath('backups/' . $name)); $i++) {
            $name = $base . '_' . $i . '.zip';
        }

        return $name;
    }
}
