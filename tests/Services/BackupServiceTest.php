<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Services\BackupService;
use Tests\TestCase;
use ZipArchive;

final class BackupServiceTest extends TestCase
{
    public function testCreateZipsDatabaseFiles(): void
    {
        file_put_contents($this->storage . '/database/users.csv', "id,login\n1,admin\n");
        file_put_contents($this->storage . '/database/.gitignore', "*\n");

        $name = new BackupService()->create();

        $this->assertMatchesRegularExpression('/^backup_\d{4}_\d{2}_\d{2}_\d{6}\.zip$/', $name);

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($this->storage . '/backups/' . $name));
        $this->assertSame(1, $zip->numFiles);
        $this->assertSame("id,login\n1,admin\n", $zip->getFromName('users.csv'));
        $zip->close();
    }

    public function testBackupsInSameSecondDoNotMerge(): void
    {
        file_put_contents($this->storage . '/database/users.csv', "id\n");
        $service = new BackupService();

        $first  = $service->create();
        file_put_contents($this->storage . '/database/stories.csv', "id\n");
        $second = $service->create();

        $this->assertNotSame($first, $second);

        $zip = new ZipArchive();
        $zip->open($this->storage . '/backups/' . $first);
        $this->assertSame(1, $zip->numFiles);
        $zip->close();
    }

    public function testExistingNameGetsSuffix(): void
    {
        file_put_contents($this->storage . '/database/users.csv', "id\n");

        do {
            $base = 'backup_' . date('Y_m_d_His');
            touch($this->storage . '/backups/' . $base . '.zip');
            $name = new BackupService()->create();
        } while (! str_starts_with($name, $base));

        $this->assertSame($base . '_2.zip', $name);
    }
}
