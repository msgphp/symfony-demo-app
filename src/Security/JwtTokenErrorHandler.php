<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTFailureEventInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class JwtTokenErrorHandler
{
    public function handle(JWTFailureEventInterface $event): void
    {
        // re-throw to apply default exception handling (either access_control or API platform)
        // in case previous exception is security related we keep leveraging it
        $exception = $event->getException();
        $previous = $exception->getPrevious();

        if (null !== $previous && $previous instanceof AuthenticationException) {
            throw $previous;
        }

        throw $exception;
    }
}
