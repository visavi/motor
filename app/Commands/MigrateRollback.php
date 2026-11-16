<?php

declare(strict_types=1);

namespace App\Commands;

use App\Exceptions\MigrationException;
use App\Services\MigrationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateRollback extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('migrate:rollback')
            ->setDescription('Migration rollback');
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
            $rolledBack = new MigrationService()->rollback(
                static fn (string $name, bool $done) => $output->writeln(
                    $done ? '<info>Rolled back:</info> ' . $name : '<comment>Rolling back:</comment> ' . $name
                )
            );
        } catch (MigrationException $e) {
            $output->writeln('<error>' . ($e->getPrevious()?->getMessage() === 'Migration file not found'
                ? 'Migration ' . $e->migration . ' not found'
                : $e->getPrevious()?->getMessage()) . '</error>');

            return Command::FAILURE;
        }

        if (! $rolledBack) {
            $output->writeln('Nothing to rollback');
        }

        return Command::SUCCESS;
    }
}
