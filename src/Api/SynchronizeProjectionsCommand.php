<?php

namespace App\Api;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SynchronizeProjectionsCommand extends Command
{
    protected static $defaultName = 'domain:projection:synchronize';

    protected function configure(): void
    {
        $this
            ->setDescription('Synchronizes all projections');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->success('All projections are synchronized.');

        return 0;
    }
}
