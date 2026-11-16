<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Exceptions\MigrationException;
use App\Models\Migration;
use App\Models\Setting;
use App\Models\User;
use App\Services\MigrationService;
use Tests\TestCase;

final class MigrationServiceTest extends TestCase
{
    private MigrationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new MigrationService($this->migrations);
    }

    public function testFreshStorageIsNotInstalledAndEverythingPending(): void
    {
        $this->addMigration('2026_01_02_b');
        $this->addMigration('2026_01_01_a');

        $this->assertFalse($this->service->isInstalled());
        $this->assertSame(['2026_01_01_a', '2026_01_02_b'], $this->service->pending());
        $this->assertCount(0, $this->service->applied());
    }

    public function testMigrateAppliesPendingInOrderAndReportsEach(): void
    {
        $this->addMigration('2026_01_01_a');
        $this->addMigration('2026_01_02_b');

        $events   = [];
        $migrated = $this->service->migrate(function (string $name, bool $done) use (&$events) {
            $events[] = [$name, $done];
        });

        $this->assertSame(['2026_01_01_a', '2026_01_02_b'], $migrated);
        $this->assertSame([
            ['2026_01_01_a', false],
            ['2026_01_01_a', true],
            ['2026_01_02_b', false],
            ['2026_01_02_b', true],
        ], $events);
        $this->assertTrue($this->service->isInstalled());
        $this->assertSame([], $this->service->pending());
        $this->assertFileExists($this->storage . '/cache/2026_01_02_b.done');
    }

    public function testMigrateWithoutPendingDoesNothing(): void
    {
        $this->addMigration('2026_01_01_a');
        $this->service->migrate();

        $this->assertSame([], $this->service->migrate());
        $this->assertSame(1, $this->runs('2026_01_01_a'));
    }

    public function testNewMigrationsGoToNextBatch(): void
    {
        $this->addMigration('2026_01_01_a');
        $this->service->migrate();
        $this->addMigration('2026_01_02_b');

        $this->assertSame(['2026_01_02_b'], $this->service->migrate());
        $this->assertSame(2, Migration::query()->where('name', '2026_01_02_b')->first()->batch);
    }

    public function testFailedMigrationThrowsAndKeepsEarlierOnes(): void
    {
        $this->addMigration('2026_01_01_a');
        $this->addMigration('2026_01_02_b', fails: true);
        $this->addMigration('2026_01_03_c');

        try {
            $this->service->migrate();
            $this->fail('MigrationException expected');
        } catch (MigrationException $e) {
            $this->assertSame('2026_01_02_b', $e->migration);
            $this->assertSame('boom', $e->getPrevious()->getMessage());
        }

        $this->assertSame(['2026_01_02_b', '2026_01_03_c'], $this->service->pending());
        $this->assertSame(0, $this->runs('2026_01_03_c'));
    }

    public function testRollbackRevertsOnlyLastBatch(): void
    {
        $this->addMigration('2026_01_01_a');
        $this->service->migrate();
        $this->addMigration('2026_01_02_b');
        $this->addMigration('2026_01_03_c');
        $this->service->migrate();

        $this->assertSame(['2026_01_03_c', '2026_01_02_b'], $this->service->rollback());
        $this->assertSame(['2026_01_02_b', '2026_01_03_c'], $this->service->pending());
        $this->assertFileExists($this->storage . '/cache/2026_01_01_a.done');
        $this->assertFileDoesNotExist($this->storage . '/cache/2026_01_02_b.done');
    }

    public function testRollbackWithoutTableDoesNothing(): void
    {
        $this->assertSame([], $this->service->rollback());
    }

    public function testRollbackFailsWhenFileIsGone(): void
    {
        $this->addMigration('2026_01_01_a');
        $this->service->migrate();
        unlink($this->migrations . '/2026_01_01_a.php');

        $this->expectException(MigrationException::class);
        $this->service->rollback();
    }

    public function testProjectMigrationsInstallFromScratch(): void
    {
        $service = new MigrationService();

        $service->migrate();

        $this->assertSame([], $service->pending());
        $this->assertGreaterThan(0, Setting::query()->count());
        $this->assertSame(0, User::query()->count());
        $this->assertFileExists($this->storage . '/database/users.csv');
    }
}
