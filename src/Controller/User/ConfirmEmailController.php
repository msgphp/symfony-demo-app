<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User\User;
use App\Entity\User\UserEmail;
use App\Http\Responder;
use App\Http\RespondRouteRedirect;
use MsgPhp\User\Command\ConfirmUserEmailCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

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
        Responder $responder,
        MessageBusInterface $bus
    ): Response {
        $bus->dispatch(new ConfirmUserEmailCommand($userEmail->getEmail()));

        return $responder->respond((new RespondRouteRedirect('profile'))->withFlashes([
            'success' => sprintf('Hi %s, your e-mail is confirmed.', $user->getEmail()),
        ]));
    }
}
