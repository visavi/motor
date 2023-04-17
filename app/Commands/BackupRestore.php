<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class BackupRestore extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('backup:restore')
            ->setDescription('Restore database')
            ->addArgument('name', InputArgument::REQUIRED, 'Archive name');

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
        $backupPath  = storagePath('backups/' . $input->getArgument('name'));

        if (! file_exists($backupPath)) {
            $output->writeln('<error>Backup ' . basename($backupPath) . ' not found</error>');

            return Command::FAILURE;
        }

        $zip = new ZipArchive();
        $result = $zip->open($backupPath);

        if ($result !== true) {
            $output->writeln('<error>' . $result . '</error>');

            return Command::FAILURE;
        }

        $zip->extractTo($databaseDir);
        $zip->close();

        $output->writeln('<info>Backup ' . basename($backupPath) . ' successfully restored</info>');

        return Command::SUCCESS;
    }
}
