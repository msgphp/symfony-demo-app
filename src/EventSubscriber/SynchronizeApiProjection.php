<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Api\Projection\Document\DocumentTransformer;
use MsgPhp\Domain\Projection\Command\DeleteProjectionDocument;
use MsgPhp\Domain\Projection\Command\SaveProjectionDocument;
use MsgPhp\User\Event\UserCreated;
use MsgPhp\User\Event\UserCredentialChanged;
use MsgPhp\User\Event\UserDeleted;
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
            $this->notifyDelete($event->user);

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

    public function notifySave($object): void
    {
        $this->bus->dispatch(new SaveProjectionDocument($this->documentTransformer->transform($object)));
    }

    public function notifyDelete($object): void
    {
        $document = $this->documentTransformer->transform($object);

        $this->bus->dispatch(new DeleteProjectionDocument($document->getType(), $document->getId()));
    }
}
