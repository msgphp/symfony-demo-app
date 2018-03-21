<?php

namespace App\Api;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SynchronizeProjectionsCommand extends Command
{
    protected static $defaultName = 'domain:projection:synchronize';

    private $synchronization;

    public function __construct(ProjectionSynchronization $synchronization)
    {
        $this->synchronization = $synchronization;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Synchronizes all projections');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->synchronization->synchronize();

        $io->success('All projections are synchronized.');

        return 0;
    }
}
