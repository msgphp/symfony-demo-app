<?php

namespace App\EventSubscriber;

use App\Entity\User\User;
use MsgPhp\User\Command\EnableUserCommand;
use MsgPhp\User\Event\UserConfirmedEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class EnableConfirmedUser implements MessageHandlerInterface
{
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(UserConfirmedEvent $event): void
    {
        $this->notify($event->user);
    }

    public function notify(User $user): void
    {
        if ($user->isEnabled()) {
            return;
        }

        $this->bus->dispatch(new EnableUserCommand($user->getId()));
    }
}
