<?php

declare(strict_types=1);

namespace App\Commands;

use App\Models\Migration;
use Exception;
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
        parent::configure();

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
        $this->createBaseTable();

        $migrations = glob(__DIR__ . '/../../database/migrations/*.php');
        $allMigrations = Migration::query()->get()->pluck('name', 'name');

        $newMigrations = [];
        foreach ($migrations as $migration) {
            $migrationName = basename($migration, '.php');
            if (! isset($allMigrations[$migrationName])) {
                $newMigrations[$migrationName] = $migration;
            }
        }

        if (! $newMigrations) {
            $output->writeln('Nothing to migrate');

            return Command::SUCCESS;
        }

        $lastMigration = Migration::query()->orderByDesc('batch')->first();

        foreach ($newMigrations as $migrationName => $migrationPath) {
            try {
                $output->writeln('<comment>Migrating:</comment> ' . $migrationName);

                $class = require_once $migrationPath;
                $class->up();
            } catch (Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');

                return Command::FAILURE;
            }

            usleep(100000);
            Migration::query()->create([
                'name'  => $migrationName,
                'batch' => $lastMigration ? $lastMigration->batch + 1 : 1,
            ]);

            $output->writeln('<info>Migrated:</info> ' . $migrationName);
        }

        return Command::SUCCESS;
    }

    /**
     * Create base table
     *
     * @return void
     */
    private function createBaseTable(): void
    {
        $migration = new \MotorORM\Migration(new Migration());
        if (! $migration->hasTable()) {
            $migration->createTable(function (\MotorORM\Migration $table) {
                $table->create('id');
                $table->create('name');
                $table->create('batch');
            });
        }
    }
}
