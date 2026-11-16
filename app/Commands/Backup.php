<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\BackupService;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        try {
            $name = new BackupService()->create();
        } catch (RuntimeException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }

        $output->writeln('<info>Backup ' . $name . ' successfully created</info>');

        return Command::SUCCESS;
    }
}
