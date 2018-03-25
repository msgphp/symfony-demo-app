<?php

namespace App\Security;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTFailureEventInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final class JwtTokenSubscriber implements EventSubscriberInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function handleSuccess(AuthenticationSuccessEvent $event): void
    {
        $event->getResponse()->headers->set('foo', 'bar');
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
