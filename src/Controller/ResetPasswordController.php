<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Http\Responder;
use App\Http\RespondRouteRedirect;
use App\Http\RespondTemplate;
use MsgPhp\User\Command\ResetUserPassword;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reset-password/{token}", name="reset_password")
 */
final class ResetPasswordController
{
    /**
     * @ParamConverter("user", converter="doctrine.orm", options={"mapping": {"token": "passwordResetToken"}})
     */
    public function __invoke(
        User $user,
        Request $request,
        Responder $responder,
        FormFactoryInterface $formFactory,
        MessageBusInterface $bus
    ): Response {
        $form = $formFactory->createNamed('', ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bus->dispatch(new ResetUserPassword($user->getId(), $form->getData()['password']));

            return $responder->respond((new RespondRouteRedirect('home'))->withFlashes([
                'success' => sprintf('Hi %s, we\'ve reset your password.', $user->getEmail()),
            ]));
        }

        return $responder->respond(new RespondTemplate('user/reset_password.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
