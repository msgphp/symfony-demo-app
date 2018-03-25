<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTFailureEventInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

final class JwtTokenErrorHandler implements EventSubscriberInterface
{
    public function handleFailure(JWTFailureEventInterface $event): void
    {
        $exception = $event->getException();

        if ($exception instanceof HttpException) {
            throw $exception;
        }

        throw new BadRequestHttpException($exception->getMessage(), $exception);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_EXPIRED => 'handleFailure',
            Events::JWT_INVALID => 'handleFailure',
            Events::JWT_NOT_FOUND => 'handleFailure',
        ];
    }
}
