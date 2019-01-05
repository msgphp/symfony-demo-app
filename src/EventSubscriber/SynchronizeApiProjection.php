<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Api\Projection\Document\DocumentTransformer;
use MsgPhp\Domain\Command\DeleteProjectionDocumentCommand;
use MsgPhp\Domain\Command\SaveProjectionDocumentCommand;
use MsgPhp\User\Event\UserCreatedEvent;
use MsgPhp\User\Event\UserCredentialChangedEvent;
use MsgPhp\User\Event\UserDeletedEvent;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class SynchronizeApiProjection implements MessageSubscriberInterface
{
    private $bus;
    private $documentTransformer;

    public function __construct(MessageBusInterface $bus, DocumentTransformer $documentTransformer)
    {
        $this->bus = $bus;
        $this->documentTransformer = $documentTransformer;
    }

    public function __invoke($event): void
    {
        if ($event instanceof UserCreatedEvent) {
            $this->notifySave($event->user);

            return;
        }

        if ($event instanceof UserCredentialChangedEvent && $event->oldCredential->getUsername() !== $event->newCredential->getUsername()) {
            // @todo could be partial update
            $this->notifySave($event->user);

            return;
        }

        if ($event instanceof UserDeletedEvent) {
            $this->notifyDelete($event->user);

            return;
        }
    }

    public static function getHandledMessages(): iterable
    {
        return [
            UserCreatedEvent::class,
            UserCredentialChangedEvent::class,
            UserDeletedEvent::class,
        ];
    }

    public function notifySave($object): void
    {
        $this->bus->dispatch(new SaveProjectionDocumentCommand($this->documentTransformer->transform($object)));
    }

    public function notifyDelete($object): void
    {
        $document = $this->documentTransformer->transform($object);

        $this->bus->dispatch(new DeleteProjectionDocumentCommand($document->getType(), $document->getId()));
    }
}
