<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Тест с отдельной временной папкой storage и папкой миграций
 */
abstract class TestCase extends BaseTestCase
{
    protected string $storage;
    protected string $migrations;

    protected function setUp(): void
    {
        $root = sys_get_temp_dir() . '/motor-test-' . bin2hex(random_bytes(6));

        $this->storage    = $root . '/storage';
        $this->migrations = $root . '/migrations';

        foreach (['database', 'backups', 'logs', 'cache'] as $dir) {
            mkdir($this->storage . '/' . $dir, 0777, true);
        }
        mkdir($this->migrations);

        putenv('MOTOR_STORAGE=' . $this->storage);
    }

    protected function tearDown(): void
    {
        putenv('MOTOR_STORAGE');

        $root  = dirname($this->storage);
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($items as $item) {
            chmod($item->getPathname(), 0777);
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($root);
    }

    /**
     * Создаёт файл миграции, которая пишет отметку в storage/cache
     *
     * @param string $name
     * @param bool   $fails бросает исключение после отметки
     */
    protected function addMigration(string $name, bool $fails = false): void
    {
        $up = $fails
            ? "throw new RuntimeException('boom');"
            : "touch(storagePath('cache/{$name}.done'));";

        file_put_contents($this->migrations . '/' . $name . '.php', <<<PHP
            <?php

            return new class
            {
                public function up(): void
                {
                    file_put_contents(storagePath('cache/{$name}.runs'), 'x', FILE_APPEND);
                    {$up}
                }

                public function down(): void
                {
                    @unlink(storagePath('cache/{$name}.done'));
                }
            };
            PHP);
    }

    /**
     * Сколько раз запускалась миграция
     */
    protected function runs(string $name): int
    {
        return strlen((string) @file_get_contents($this->storage . '/cache/' . $name . '.runs'));
    }

    /**
     * Архивы в storage/backups
     *
     * @return string[]
     */
    protected function backups(): array
    {
        return array_map('basename', glob($this->storage . '/backups/*.zip') ?: []);
    }
}
