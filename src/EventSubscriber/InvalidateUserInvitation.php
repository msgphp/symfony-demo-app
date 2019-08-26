<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\UserInvitation;
use Doctrine\ORM\EntityManagerInterface;
use MsgPhp\User\Event\UserCreated;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class InvalidateUserInvitation implements MessageHandlerInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(UserCreated $event): void
    {
        if (!isset($event->context['invitation_token']) || null === $invitation = $this->em->find(UserInvitation::class, $event->context['invitation_token'])) {
            return;
        }

        $this->em->remove($invitation);
        $this->em->flush();
    }
}
