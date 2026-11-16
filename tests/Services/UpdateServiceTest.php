<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Services\MigrationService;
use App\Services\UpdateService;
use Tests\TestCase;

final class UpdateServiceTest extends TestCase
{
    private UpdateService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new UpdateService(new MigrationService($this->migrations));
        $this->addMigration('2026_01_01_a');
    }

    public function testFirstRequestInstallsWithoutBackup(): void
    {
        $page = $this->service->handle('/');

        $this->assertStringContainsString('Установка завершена', $page);
        $this->assertTrue($this->service->isUpToDate());
        $this->assertSame([], $this->backups());
    }

    public function testInstalledSiteOpensAsUsual(): void
    {
        $this->service->handle('/');

        $this->assertNull($this->service->handle('/'));
        $this->assertSame(1, $this->runs('2026_01_01_a'));
    }

    public function testUpdateMakesBackupAndAppliesNewMigrations(): void
    {
        $this->service->handle('/');
        $this->addMigration('2026_01_02_b');

        $this->assertFalse($this->service->isUpToDate());
        $this->assertNull($this->service->handle('/stories'));
        $this->assertTrue($this->service->isUpToDate());
        $this->assertCount(1, $this->backups());
    }

    public function testFailedUpdateShowsMaintenancePageAndRecordsFailure(): void
    {
        $this->service->handle('/');
        $this->addMigration('2026_01_02_b', fails: true);

        $page    = $this->service->handle('/');
        $failure = $this->service->failure();

        $this->assertStringContainsString('Сайт обновляется', $page);
        $this->assertStringNotContainsString('boom', $page);
        $this->assertSame('2026_01_02_b', $failure['migration']);
        $this->assertSame('boom', $failure['message']);
        $this->assertSame($this->backups(), [$failure['backup']]);
        $this->assertStringContainsString('2026_01_02_b', file_get_contents($this->storage . '/logs/motor-' . date('Y-m-d') . '.log'));
    }

    public function testKnownFailureIsNotRetriedOnEveryRequest(): void
    {
        $this->service->handle('/');
        $this->addMigration('2026_01_02_b', fails: true);
        $this->service->handle('/');

        $this->assertStringContainsString('Сайт обновляется', $this->service->handle('/'));
        $this->assertSame(1, $this->runs('2026_01_02_b'));
        $this->assertCount(1, $this->backups());
    }

    public function testLoginAndMigrationsPageOpenAfterFailure(): void
    {
        $this->service->handle('/');
        $this->addMigration('2026_01_02_b', fails: true);

        $this->assertNull($this->service->handle('/admin/migrations'));
        $this->assertNull($this->service->handle('/login/'));
        $this->assertNotNull($this->service->handle('/admin'));
    }

    public function testFixedMigrationRunsAgainAndClearsFailure(): void
    {
        $this->service->handle('/');
        $this->addMigration('2026_01_02_b', fails: true);
        $this->service->handle('/');

        $this->addMigration('2026_01_02_b');

        $this->assertNull($this->service->handle('/'));
        $this->assertNull($this->service->failure());
        $this->assertTrue($this->service->isUpToDate());
    }

    public function testPartialFailureIsNotRetried(): void
    {
        $this->service->handle('/');
        $this->addMigration('2026_01_02_b');
        $this->addMigration('2026_01_03_c', fails: true);

        $this->service->handle('/');
        $this->service->handle('/');

        $this->assertSame(['2026_01_03_c'], $this->service->failure()['pending']);
        $this->assertSame(1, $this->runs('2026_01_03_c'));
        $this->assertCount(1, $this->backups());
    }

    public function testNotWritableStorageShowsListOfDirs(): void
    {
        chmod($this->storage . '/database', 0555);

        $page = $this->service->handle('/');

        $this->assertStringContainsString('storage/database', $page);
        $this->assertStringNotContainsString('storage/backups', $page);
        $this->assertSame(0, $this->runs('2026_01_01_a'));
    }
}
