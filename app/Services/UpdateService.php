<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\MigrationException;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger;
use Throwable;

/**
 * Установка и обновление таблиц при обращении к сайту
 */
final class UpdateService
{
    public const string FAILED_FILE = 'logs/migrate-failed.json';

    private const string LOCK_FILE = 'cache/migrate.lock';

    /**
     * Страницы, которые открываются и после сбоя обновления
     */
    private const array BYPASS = ['/admin/migrations', '/login'];

    public function __construct(
        private readonly MigrationService $migrationService = new MigrationService(),
        private readonly BackupService $backupService = new BackupService(),
    ) {}

    /**
     * Устанавливает или обновляет таблицы, если нужно
     *
     * @param string $path адрес запроса
     *
     * @return string|null html страницы вместо сайта или null, если сайт можно открывать
     */
    public function handle(string $path = '/'): ?string
    {
        if ($this->isUpToDate()) {
            return null;
        }

        $bypass = in_array(rtrim($path, '/'), self::BYPASS, true);

        if ($this->isKnownFailure()) {
            return $bypass ? null : $this->page('failed', 503);
        }

        $notWritable = $this->notWritable();
        if ($notWritable) {
            return $this->page('not-writable', 503, ['dirs' => $notWritable]);
        }

        $lock = fopen(storagePath(self::LOCK_FILE), 'c');
        flock($lock, LOCK_EX);

        try {
            if ($this->isUpToDate()) {
                return null;
            }

            if ($this->isKnownFailure()) {
                return $bypass ? null : $this->page('failed', 503);
            }

            $installing = ! $this->migrationService->isInstalled();

            if (! $this->run($installing)) {
                return $bypass ? null : $this->page('failed', 503);
            }
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }

        return $installing ? $this->page('installed', 200) : null;
    }

    /**
     * Все ли файлы миграций записаны в таблицу миграций
     *
     * @return bool
     */
    public function isUpToDate(): bool
    {
        $handle = @fopen(storagePath('database/migrations.csv'), 'r');
        if (! $handle) {
            return false;
        }

        $applied = [];
        $column  = array_search('name', fgetcsv($handle, escape: '') ?: [], true);

        while ($column !== false && ($row = fgetcsv($handle, escape: '')) !== false) {
            $applied[$row[$column] ?? ''] = true;
        }

        fclose($handle);

        foreach (array_keys($this->migrationService->files()) as $name) {
            if (! isset($applied[$name])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Последний сбой обновления
     *
     * @return array|null
     */
    public function failure(): ?array
    {
        $file = storagePath(self::FAILED_FILE);

        if (! is_file($file)) {
            return null;
        }

        $failure = json_decode((string) file_get_contents($file), true);

        return is_array($failure) ? $failure : null;
    }

    /**
     * Забывает сбой обновления
     *
     * @return void
     */
    public function clearFailure(): void
    {
        $file = storagePath(self::FAILED_FILE);

        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * Выполняет миграции, при обновлении — после бэкапа
     *
     * @param bool $installing
     *
     * @return bool
     */
    private function run(bool $installing): bool
    {
        $backup = null;

        try {
            $backup = $installing ? null : $this->backupService->create();
            $this->migrationService->migrate();
            $this->clearFailure();

            return true;
        } catch (Throwable $e) {
            $this->logger()->error('Обновление таблиц не выполнено', ['exception' => $e, 'backup' => $backup]);

            $pending = $this->migrationService->pending();

            file_put_contents(storagePath(self::FAILED_FILE), json_encode([
                'migration' => $e instanceof MigrationException ? $e->migration : null,
                'message'   => $e instanceof MigrationException ? $e->getPrevious()?->getMessage() : $e->getMessage(),
                'backup'    => $backup,
                'time'      => date('Y-m-d H:i:s'),
                'pending'   => $pending,
                'signature' => $this->signature($pending),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return false;
        }
    }

    /**
     * Был ли уже сбой на тех же самых миграциях
     *
     * @return bool
     */
    private function isKnownFailure(): bool
    {
        $failure = $this->failure();

        return $failure !== null
            && ($failure['signature'] ?? null) === $this->signature($this->migrationService->pending());
    }

    /**
     * Отпечаток ожидающих миграций: имена и содержимое файлов
     *
     * @param string[] $pending
     *
     * @return string
     */
    private function signature(array $pending): string
    {
        $files = $this->migrationService->files();
        $parts = [];

        foreach ($pending as $name) {
            $parts[] = $name . ':' . (isset($files[$name]) ? md5_file($files[$name]) : '');
        }

        return md5(implode('|', $parts));
    }

    /**
     * Папки, в которые сайт не может писать
     *
     * @return string[]
     */
    private function notWritable(): array
    {
        $dirs = [
            'storage/database' => storagePath('database'),
            'storage/backups'  => storagePath('backups'),
            'storage/logs'     => storagePath('logs'),
            'storage/cache'    => storagePath('cache'),
            'public/uploads'   => publicPath('uploads'),
        ];

        foreach (glob(publicPath('uploads/*'), GLOB_ONLYDIR) ?: [] as $dir) {
            $dirs['public/uploads/' . basename($dir)] = $dir;
        }

        return array_keys(array_filter($dirs, static fn (string $dir) => ! is_writable($dir)));
    }

    /**
     * Html служебной страницы
     *
     * @param string $name
     * @param int    $code
     * @param array  $params
     *
     * @return string
     */
    private function page(string $name, int $code, array $params = []): string
    {
        http_response_code($code);

        if ($code === 503) {
            header('Retry-After: 60');
        }

        extract($params);
        ob_start();
        require basePath('resources/views/install/' . $name . '.php');

        return (string) ob_get_clean();
    }

    /**
     * Журнал
     *
     * @return Logger
     */
    private function logger(): Logger
    {
        $logger = new Logger('motor');
        $logger->pushHandler(new RotatingFileHandler(storagePath('logs/motor.log'), 7, Level::Debug));

        return $logger;
    }
}
