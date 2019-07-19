<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Api\DocumentIdentity;
use MsgPhp\Domain\DomainId;
use MsgPhp\Domain\Projection\Command\DeleteProjection;
use MsgPhp\Domain\Projection\Command\SaveProjection;
use MsgPhp\User\Event\UserCreated;
use MsgPhp\User\Event\UserCredentialChanged;
use MsgPhp\User\Event\UserDeleted;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class SynchronizeApi implements MessageSubscriberInterface
{
    private $bus;
    private $documentTransformer;
    private $documentTypeResolver;

    public function __construct(MessageBusInterface $bus, callable $documentTransformer, callable $documentTypeResolver)
    {
        $this->bus = $bus;
        $this->documentTransformer = $documentTransformer;
        $this->documentTypeResolver = $documentTypeResolver;
    }

    public function __invoke($event): void
    {
        if ($event instanceof UserCreated) {
            $this->notifySave($event->user);

            return;
        }

        if ($event instanceof UserCredentialChanged && $event->oldCredential->getUsername() !== $event->user->getCredential()->getUsername()) {
            // @todo could be partial update
            $this->notifySave($event->user);

            return;
        }

        if ($event instanceof UserDeleted) {
            $this->notifyDelete($event->user, $event->user->getId());

            return;
        }
    }

    public static function getHandledMessages(): iterable
    {
        return [
            UserCreated::class,
            UserCredentialChanged::class,
            UserDeleted::class,
        ];
    }

    private function notifySave(object $object): void
    {
        $this->bus->dispatch(new SaveProjection(($this->documentTypeResolver)($object), ($this->documentTransformer)($object)));
    }

    private function notifyDelete(object $object, DomainId $id): void
    {
        $this->bus->dispatch(new DeleteProjection(($this->documentTypeResolver)($object), DocumentIdentity::get($id)));
    }
}
