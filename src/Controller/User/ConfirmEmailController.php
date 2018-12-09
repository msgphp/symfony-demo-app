<?php

namespace App\Controller\User;

use App\Entity\User\User;
use App\Entity\User\UserEmail;
use MsgPhp\User\Command\ConfirmUserEmailCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/profile/confirm-email/{token}", name="confirm_email")
 */
final class ConfirmEmailController
{
    /**
     * @ParamConverter("user", converter="msgphp.current_user")
     * @ParamConverter("userEmail", converter="doctrine.orm", options={"mapping": {"token": "confirmationToken"}})
     */
    public function __invoke(
        User $user,
        UserEmail $userEmail,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        MessageBusInterface $bus
    ): Response
    {
        $bus->dispatch(new ConfirmUserEmailCommand($userEmail->getEmail()));
        $flashBag->add('success', sprintf('Hi %s, your e-mail is confirmed.', $user->getEmail()));

        return new RedirectResponse($urlGenerator->generate('profile'));
    }
}
