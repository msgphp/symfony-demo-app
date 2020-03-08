<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Entity\UserInvitation;
use Doctrine\ORM\EntityManagerInterface;
use MsgPhp\User\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;

final class InviteUserCommand extends Command
{
    protected static $defaultName = 'user:invite';

    private $em;
    private $userRepository;
    private $mailer;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository, MailerInterface $mailer)
    {
        parent::__construct();

        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
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

        /** @psalm-suppress PossiblyInvalidCast */
        $email = (string) $input->getArgument('email');

        if ($this->userRepository->usernameExists($email) || null !== $this->em->getRepository(UserInvitation::class)->findOneBy(['email' => $email])) {
            throw new \LogicException(sprintf('The username "%s" already exists.', $email));
        }

        $invitation = new UserInvitation($email, $token);

        $this->em->persist($invitation);
        $this->em->flush();

        $io->success(sprintf('Created registration token "%s" for user "%s".', $invitation->getToken(), $invitation->getEmail()));

        if ($input->getOption('notify')) {
            $params = compact('invitation');
            $message = (new TemplatedEmail())
                ->from('webmaster@localhost')
                ->to($email)
                ->subject('You are invited to register at The App')
                ->textTemplate('user/email/invited.txt.twig')
                ->htmlTemplate('user/email/invited.html.twig')
                ->context($params)
            ;

            $this->mailer->send($message);
            $io->note('Notification sent');
        } else {
            $io->note('No notification was sent');
        }

        return 0;
    }
}
