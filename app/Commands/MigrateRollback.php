<?php

declare(strict_types=1);

namespace App\Commands;

use App\Models\Migration;
use Exception;
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
        $lastMigration = Migration::query()->orderByDesc('batch')->first();
        if (! $lastMigration) {
            $output->writeln('Nothing to rollback');

            return Command::SUCCESS;
        }

        $migrations = Migration::query()
            ->where('batch', $lastMigration->batch)
            ->orderByDesc('id')
            ->get();

        foreach ($migrations as $migration) {
            $migrationPath = __DIR__ . '/../../database/migrations/' . $migration->name . '.php';

            if (! file_exists($migrationPath)) {
                $output->writeln('<error>Migration ' . $migration->name . ' not found</error>');

                return Command::FAILURE;
            }

            try {
                $output->writeln('<comment>Rolling back:</comment> ' . $migration->name);

                $class = require_once $migrationPath;
                $class->down();
            } catch (Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');

                return Command::FAILURE;
            }

            usleep(100000);
            $migration->delete();

            $output->writeln('<info>Rolled back:</info> ' . $migration->name);
        }

        return Command::SUCCESS;
    }
}
