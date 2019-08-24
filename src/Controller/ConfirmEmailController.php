<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserEmail;
use App\Http\Responder;
use App\Http\RespondRouteRedirect;
use MsgPhp\User\Command\ConfirmUserEmail;
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
        $bus->dispatch(new ConfirmUserEmail($userEmail->getEmail()));

        return $responder->respond((new RespondRouteRedirect('profile'))->withFlashes([
            'success' => sprintf('Hi %s, your e-mail is confirmed.', $user->getEmail()),
        ]));
    }
}
