<?php

namespace App\EventSubscriber;

use App\Entity\User\User;
use MsgPhp\User\Entity\Credential\EmailPassword;
use MsgPhp\User\Event\UserCredentialChangedEvent;
use MsgPhp\User\Repository\UserRepositoryInterface;

final class ClearPasswordResetToken
{
    private $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UserCredentialChangedEvent $event): void
    {
        if (!$this->isPasswordChanged($event->oldCredential, $event->newCredential)) {
            return;
        }

        $this->notify($event->user);
    }

    public function notify(User $user): void
    {
        if (null === $user->getPasswordResetToken()) {
            return;
        }

        $user->clearPasswordRequest();

        $this->repository->save($user);
    }

    private function isPasswordChanged(EmailPassword $oldCredential, EmailPassword $newCredential): bool
    {
        return $oldCredential->getPassword() !== $newCredential->getPassword();
    }
}
