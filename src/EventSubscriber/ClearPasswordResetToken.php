<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User\User;
use MsgPhp\User\Credential\EmailPassword;
use MsgPhp\User\Event\UserCredentialChanged;
use MsgPhp\User\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ClearPasswordResetToken implements MessageHandlerInterface
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UserCredentialChanged $event): void
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
