<?php

declare(strict_types=1);

namespace App\Security;

use App\Api\DocumentIdentity;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTFailureEventInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class JwtTokenSubscriber implements EventSubscriberInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function handleSuccess(AuthenticationSuccessEvent $event): void
    {
        $docId = DocumentIdentity::get($event->getUser()->getUsername());
        $locationUrl = $this->urlGenerator->generate('api_users_get_item', ['id' => $docId], UrlGeneratorInterface::ABSOLUTE_URL);

        $event->getResponse()->headers->set('Location', $locationUrl);
    }

    public function handleFailure(JWTFailureEventInterface $event): void
    {
        $exception = $event->getException();

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
