<?php

namespace App\Api;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SynchronizeProjectionsCommand extends Command
{
    protected static $defaultName = 'domain:projection:synchronize';

    private $synchronization;
    private $logger;

    public function __construct(ProjectionSynchronization $synchronization, LoggerInterface $logger)
    {
        $this->synchronization = $synchronization;
        $this->logger = $logger ?? new NullLogger();

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
        $succeed = $failed = 0;

        foreach ($this->synchronization->synchronize() as $document) {
            if (ProjectionDocument::STATUS_VALID === $document->status) {
                ++$succeed;
            } else {
                ++$failed;
            }

            if (null !== $document->error) {
                $this->logger->error($document->error->getMessage(), ['exception' => $document->error]);
            }
        }

        $io->success($succeed.' projection '.(1 === $succeed ? 'document' : 'documents').' synchronized');

        if ($failed) {
            $io->error($failed.' projection '.(1 === $failed ? 'document' : 'documents').' failed');
        }

        return 0;
    }
}
