<?php

namespace App\Controller\User;

use App\Entity\User\User;
use MsgPhp\User\Command\ConfirmUserCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/register/confirm/{token}", name="register_confirm")
 */
final class ConfirmRegistrationController
{
    /**
     * @ParamConverter("user", converter="doctrine.orm", options={"mapping": {"token": "confirmationToken"}})
     */
    public function __invoke(
        User $user,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        MessageBusInterface $bus
    ): Response
    {
        $bus->dispatch(new ConfirmUserCommand($user->getId()));
        $flashBag->add('success', sprintf('Hi %s, your registration is confirmed. You can now login.', $user->getCredential()->getUsername()));

        return new RedirectResponse($urlGenerator->generate('login'));
    }
}
