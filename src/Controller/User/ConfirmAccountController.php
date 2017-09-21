<?php

namespace App\Controller\User;

use MsgPhp\Domain\CommandBusInterface;
use MsgPhp\User\Command\ConfirmPendingUserCommand;
use MsgPhp\User\Infra\Uuid\UserId;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ConfirmAccountController
{
    public function __invoke(
        string $token,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        CommandBusInterface $commandBus
    ): Response
    {
        $commandBus->handle(new ConfirmPendingUserCommand($token, new UserId()));
        $flashBag->add('success', sprintf('Hi, your account is confirmed. You can now login.'));

        return new RedirectResponse($urlGenerator->generate('login'));
    }
}
