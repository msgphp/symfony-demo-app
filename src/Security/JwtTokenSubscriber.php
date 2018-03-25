<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTFailureEventInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use PascalDeVink\ShortUuid\ShortUuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class JwtTokenSubscriber implements EventSubscriberInterface
{
    // @todo inject as parameter, should be changed per app and is secret
    private const DOCUMENT_UUID_NS = 'ee5b8c83-f12d-41f5-bcf9-3e83b7558317';

    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function handleSuccess(AuthenticationSuccessEvent $event): void
    {
        $userId = $event->getUser()->getUsername();
        $docId = ShortUuid::uuid5(self::DOCUMENT_UUID_NS, sha1($userId));

        $event->getResponse()->headers->set('Location', $this->urlGenerator->generate('api_users_get_item', ['id' => $docId], UrlGeneratorInterface::ABSOLUTE_URL));
    }

    public function handleFailure(JWTFailureEventInterface $event): void
    {
        $exception = $event->getException();

        if ($exception instanceof HttpException) {
            throw $exception;
        }

        throw new UnauthorizedHttpException('Bearer', $exception->getMessage(), $exception);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'handleSuccess',
            Events::JWT_EXPIRED => 'handleFailure',
            Events::JWT_INVALID => 'handleFailure',
            Events::JWT_NOT_FOUND => 'handleFailure',
        ];
    }
}
