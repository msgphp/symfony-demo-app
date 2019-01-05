<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Entity\User\OneTimeLoginToken;
use Doctrine\ORM\EntityManagerInterface;
use MsgPhp\User\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CreateOneTimeLoginTokenCommand extends Command
{
    protected static $defaultName = 'user:one-time-login-token';

    private $em;
    private $userRepository;

    public function __construct(EntityManagerInterface $em, UserRepositoryInterface $userRepository)
    {
        parent::__construct();

        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create a one-time login token')
            ->addArgument('username', InputArgument::REQUIRED, 'Username to provide the token for')
            ->addOption('token', null, InputOption::VALUE_REQUIRED, 'A pre-defined token, or a generated on otherwise')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $token = $input->getOption('token');

        if (null !== $token) {
            if (!is_scalar($token)) {
                throw new \UnexpectedValueException('Unexpected token');
            }

            $token = (string) $token;

            if (null !== $this->em->find(OneTimeLoginToken::class, $token)) {
                throw new \LogicException(sprintf('The token "%s" already exists.', $token));
            }
        }

        $username = $input->getArgument('username');
        $user = $this->userRepository->findByUsername($username);
        $oneTimeLoginToken = new OneTimeLoginToken($user, $token);

        $this->em->persist($oneTimeLoginToken);
        $this->em->flush();

        $io->success(sprintf('Created token "%s" for user "%s".', $oneTimeLoginToken->getToken(), $oneTimeLoginToken->getUser()->getCredential()->getUsername()));

        return 0;
    }
}
