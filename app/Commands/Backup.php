<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class Backup extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('backup')
            ->setDescription('Backup database');
    }

    /**
     * Run
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $databaseDir = storagePath('database/');
        $backupPath  = storagePath('backups/backup_' . date('Y_m_d_His') . '.zip');

        $zip = new ZipArchive();
        $result = $zip->open($backupPath, ZipArchive::CREATE);

        if ($result !== true) {
            $output->writeln('<error>' . $result . '</error>');

            return Command::FAILURE;
        }

        $dir = opendir($databaseDir);

        while ($file = readdir($dir)) {
            if (str_starts_with($file, '.')) {
                continue;
            }

            if (! is_file($databaseDir . $file)) {
                continue;
            }

            $zip->addFile($databaseDir . $file, $file);
        }

        $zip->close();

        $output->writeln('<info>Backup ' . basename($backupPath) . ' successfully created</info>');

        return Command::SUCCESS;
    }
}
