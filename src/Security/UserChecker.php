<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use MsgPhp\User\Infra\Security\SecurityUser;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $em, ?LoggerInterface $logger = null)
    {
        $this->em = $em;
        $this->logger = $logger ?? new NullLogger();
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof SecurityUser) {
            return;
        }

        $userId = $user->getUserId();

        /** @var User|null $user */
        $user = $this->em->find(User::class, $userId);

        if (null === $user) {
            throw new AuthenticationCredentialsNotFoundException('Bad credentials.');
        }

        if (!$user->isEnabled()) {
            $this->logger->info('Disabled user login attempt.', ['id' => $userId->toString(), 'email' => $user->getEmail()]);

            throw new DisabledException('Bad credentials.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
