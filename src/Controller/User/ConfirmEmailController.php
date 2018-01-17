<?php

namespace App\Controller\User;

use App\Entity\User\User;
use App\Entity\User\UserSecondaryEmail;
use MsgPhp\Domain\CommandBusInterface;
use MsgPhp\User\Command\ConfirmUserSecondaryEmailCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ConfirmEmailController
{
    /**
     * @ParamConverter("userSecondaryEmail", options={"mapping": {"token": "confirmationToken"}})
     */
    public function __invoke(
        User $user,
        UserSecondaryEmail $userSecondaryEmail,
        TokenStorageInterface $tokenStorage,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        CommandBusInterface $commandBus
    ): Response
    {
        if (!$userSecondaryEmail->getUserId()->equals($user->getId())) {
            throw new NotFoundHttpException();
        }

        $wasPendingPrimary = $userSecondaryEmail->isPendingPrimary();

        $commandBus->handle(new ConfirmUserSecondaryEmailCommand($userSecondaryEmail->getConfirmationToken()));

        if ($wasPendingPrimary) {
            $flashBag->add('success', sprintf('Hi, your e-mail is confirmed. Please login again.'));
            $tokenStorage->setToken(null);

            return new RedirectResponse($urlGenerator->generate('login'));
        }

        $flashBag->add('success', 'Hi, your e-mail is confirmed.');

        return new RedirectResponse($urlGenerator->generate('my_account'));
    }
}
