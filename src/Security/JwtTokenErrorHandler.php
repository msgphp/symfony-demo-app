<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTFailureEventInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class JwtTokenErrorHandler
{
    public function handle(JWTFailureEventInterface $event): void
    {
        $exception = $event->getException();

        throw new BadRequestHttpException($exception->getMessage(), $exception);
    }
}
