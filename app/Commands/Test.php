<?php

declare(strict_types=1);

namespace App\Commands;

use App\Models\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Test extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName('test')
            ->setDescription('Test command');
    }

    /**
     * Test
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        dd(User::query()->first());
        $output->writeln('<info>Test success</info>');

        return Command::SUCCESS;
    }
}
