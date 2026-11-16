<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\MigrationException;
use App\Models\Migration;
use MotorORM\Collection;
use MotorORM\Migration as TableMigration;
use RuntimeException;
use Throwable;

/**
 * Выполнение и откат миграций
 */
class MigrationService
{
    private string $migrationsPath;

    public function __construct(?string $migrationsPath = null)
    {
        $this->migrationsPath = $migrationsPath ?? basePath('database/migrations');
    }

    /**
     * Создана ли таблица миграций
     *
     * @return bool
     */
    public function isInstalled(): bool
    {
        return file_exists(storagePath('database/migrations.csv'));
    }

    /**
     * Файлы миграций по возрастанию имени
     *
     * @return array<string, string> имя => путь
     */
    public function files(): array
    {
        $files = [];

        foreach (glob($this->migrationsPath . '/*.php') ?: [] as $path) {
            $files[basename($path, '.php')] = $path;
        }

        ksort($files);

        return $files;
    }

    /**
     * Невыполненные миграции
     *
     * @return string[]
     */
    public function pending(): array
    {
        $applied = $this->isInstalled()
            ? Migration::query()->get()->pluck('name', 'name')->all()
            : [];

        return array_values(array_diff(array_keys($this->files()), array_keys($applied)));
    }

    /**
     * Выполненные миграции, новые сверху
     *
     * @return Collection<Migration>
     */
    public function applied(): Collection
    {
        if (! $this->isInstalled()) {
            return new Collection();
        }

        return Migration::query()->orderByDesc('id')->get();
    }

    /**
     * Выполняет невыполненные миграции одной партией
     *
     * @param callable(string, bool): void|null $onEach вызывается перед миграцией (false) и после неё (true)
     *
     * @return string[] выполненные миграции
     *
     * @throws MigrationException
     */
    public function migrate(?callable $onEach = null): array
    {
        $this->createTable();

        $pending = $this->pending();
        if (! $pending) {
            return [];
        }

        $files     = $this->files();
        $lastBatch = Migration::query()->orderByDesc('batch')->first();
        $batch     = $lastBatch ? $lastBatch->batch + 1 : 1;
        $migrated  = [];

        foreach ($pending as $name) {
            $onEach && $onEach($name, false);

            try {
                $class = require $files[$name];
                $class->up();
            } catch (Throwable $e) {
                throw new MigrationException($name, $e);
            }

            usleep(100000);
            Migration::query()->create([
                'name'  => $name,
                'batch' => $batch,
            ]);

            $migrated[] = $name;
            $onEach && $onEach($name, true);
        }

        return $migrated;
    }

    /**
     * Откатывает последнюю партию миграций
     *
     * @param callable(string, bool): void|null $onEach вызывается перед откатом (false) и после него (true)
     *
     * @return string[] откаченные миграции
     *
     * @throws MigrationException
     */
    public function rollback(?callable $onEach = null): array
    {
        if (! $this->isInstalled()) {
            return [];
        }

        $lastBatch = Migration::query()->orderByDesc('batch')->first();
        if (! $lastBatch) {
            return [];
        }

        $migrations = Migration::query()
            ->where('batch', $lastBatch->batch)
            ->orderByDesc('id')
            ->get();

        $rolledBack = [];

        foreach ($migrations as $migration) {
            $path = $this->migrationsPath . '/' . $migration->name . '.php';

            if (! file_exists($path)) {
                throw new MigrationException($migration->name, new RuntimeException('Migration file not found'));
            }

            $onEach && $onEach($migration->name, false);

            try {
                $class = require $path;
                $class->down();
            } catch (Throwable $e) {
                throw new MigrationException($migration->name, $e);
            }

            usleep(100000);
            $migration->delete();

            $rolledBack[] = $migration->name;
            $onEach && $onEach($migration->name, true);
        }

        return $rolledBack;
    }

    /**
     * Создаёт таблицу миграций, если её нет
     *
     * @return void
     */
    private function createTable(): void
    {
        $migration = new TableMigration(new Migration());

        if (! $migration->hasTable()) {
            $migration
                ->create('id')
                ->create('name')
                ->create('batch')
                ->createTable();
        }
    }
}
