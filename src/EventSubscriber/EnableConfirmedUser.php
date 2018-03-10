<?php

namespace App\EventSubscriber;

use App\Entity\User\User;
use MsgPhp\Domain\Message\MessageDispatchingTrait;
use MsgPhp\User\Command\EnableUserCommand;
use MsgPhp\User\Event\UserConfirmedEvent;

final class EnableConfirmedUser
{
    use MessageDispatchingTrait;

    public function __invoke(UserConfirmedEvent $event): void
    {
        $this->notify($event->user);
    }

    public function notify(User $user): void
    {
        if ($user->isEnabled()) {
            return;
        }

        $this->dispatch(EnableUserCommand::class, [$user->getId()]);
    }
}
