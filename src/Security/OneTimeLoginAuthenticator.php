<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User\OneTimeLoginToken;
use Doctrine\ORM\EntityManagerInterface;
use MsgPhp\User\Infrastructure\Security\SecurityUser;
use MsgPhp\User\Infrastructure\Security\SecurityUserProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

final class OneTimeLoginAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $userProvider;
    private $urlGenerator;

    public function __construct(EntityManagerInterface $em, SecurityUserProvider $userProvider, UrlGeneratorInterface $urlGenerator)
    {
        $this->em = $em;
        $this->userProvider = $userProvider;
        $this->urlGenerator = $urlGenerator;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->urlGenerator->generate('login'));
    }

    public function supports(Request $request): bool
    {
        return 'login' === $request->attributes->get('_route')
            && $request->isMethod(Request::METHOD_GET)
            && $request->query->has('token');
    }

    public function getCredentials(Request $request)
    {
        return $request->query->get('token');
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $oneTimeLoginToken = $this->getOneTimeLoginToken($credentials);

        if (null === $oneTimeLoginToken) {
            return null;
        }

        return $this->userProvider->fromUser($oneTimeLoginToken->getUser());
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if (!$user instanceof SecurityUser) {
            return false;
        }

        $oneTimeLoginToken = $this->getOneTimeLoginToken($credentials);

        if (null === $oneTimeLoginToken) {
            return false;
        }

        return $oneTimeLoginToken->getUserId()->equals($user->getUserId());
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('login'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        $oneTimeLoginToken = $this->getOneTimeLoginTokenOnce($this->getCredentials($request));

        if (null === $oneTimeLoginToken) {
            return null;
        }

        return new RedirectResponse($oneTimeLoginToken->getRedirectUrl() ?? $this->urlGenerator->generate('profile'));
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    private function getOneTimeLoginToken(string $token): ?OneTimeLoginToken
    {
        return $this->em->find(OneTimeLoginToken::class, $token);
    }

    private function getOneTimeLoginTokenOnce(string $token): ?OneTimeLoginToken
    {
        $token = $this->getOneTimeLoginToken($token);

        $this->em->remove($token);
        $this->em->flush();

        return $token;
    }
}
