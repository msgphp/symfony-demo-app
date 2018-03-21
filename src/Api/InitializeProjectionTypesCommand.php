<?php

namespace App\Api;

use Elasticsearch\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InitializeProjectionTypesCommand extends Command
{
    protected static $defaultName = 'domain:projection:initialize-types';

    private $registries;

    /**
     * @param ProjectionTypeRegistry[] $registries
     */
    public function __construct(iterable $registries)
    {
        $this->registries = $registries;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Initializes all projection types')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force initialization by resetting types first');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $force = $input->getOption('force');

        foreach ($this->registries as $registry) {
            if ($force) {
                $registry->destroy();
            }

            $registry->initialize();
        }

        $io->success('All projection types are initialized.');

        return 0;
    }
}
