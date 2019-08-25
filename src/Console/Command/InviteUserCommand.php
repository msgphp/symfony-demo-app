<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Entity\UserInvitation;
use Doctrine\ORM\EntityManagerInterface;
use MsgPhp\User\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

final class InviteUserCommand extends Command
{
    protected static $defaultName = 'user:invite';

    private $em;
    private $userRepository;
    private $mailer;
    private $twig;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository, \Swift_Mailer $mailer, Environment $twig)
    {
        parent::__construct();

        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Invite a user for registration')
            ->addArgument('email', InputArgument::REQUIRED, 'An e-mail to provide the token for')
            ->addOption('token', null, InputOption::VALUE_REQUIRED, 'A pre-defined token (will be generated otherwise)')
            ->addOption('notify', null, InputOption::VALUE_NONE, 'Send notification to the invited e-mail')
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

            if (null !== $this->em->find(UserInvitation::class, $token)) {
                throw new \LogicException(sprintf('The token "%s" already exists.', $token));
            }
        }

        $email = $input->getArgument('email');

        if ($this->userRepository->usernameExists($email) || null !== $this->em->getRepository(UserInvitation::class)->findOneBy(['email' => $email])) {
            throw new \LogicException(sprintf('The username "%s" already exists.', $email));
        }

        $invitation = new UserInvitation($email, $token);

        $this->em->persist($invitation);
        $this->em->flush();

        $io->success(sprintf('Created registration token "%s" for user "%s".', $invitation->getToken(), $invitation->getEmail()));

        if ($input->getOption('notify')) {
            $params = compact('invitation');
            $message = (new \Swift_Message('You are invited to register at The App'))
                ->addTo($email)
                ->setBody($this->twig->render('user/email/invited.txt.twig', $params), 'text/plain')
                ->addPart($this->twig->render('user/email/invited.html.twig', $params), 'text/html')
            ;

            $this->mailer->send($message);
            $io->note('Notification sent');
        } else {
            $io->note('No notification was sent');
        }

        return 0;
    }
}
