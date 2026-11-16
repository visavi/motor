<?php

declare(strict_types=1);

namespace App\Commands;

use App\Exceptions\MigrationException;
use App\Services\MigrationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('migrate')
            ->setDescription('Run migration');
    }

    /**
     * Run migration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $migrated = new MigrationService()->migrate(
                static fn (string $name, bool $done) => $output->writeln(
                    $done ? '<info>Migrated:</info> ' . $name : '<comment>Migrating:</comment> ' . $name
                )
            );
        } catch (MigrationException $e) {
            $output->writeln('<error>' . $e->getPrevious()?->getMessage() . '</error>');

            return Command::FAILURE;
        }

        if (! $migrated) {
            $output->writeln('Nothing to migrate');
        }

        return Command::SUCCESS;
    }
}
