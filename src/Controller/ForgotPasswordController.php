<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Http\Responder;
use App\Http\RespondRouteRedirect;
use App\Http\RespondTemplate;
use MsgPhp\User\Command\RequestUserPassword;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/forgot-password", name="forgot_password")
 */
final class ForgotPasswordController
{
    public function __invoke(
        Request $request,
        Responder $responder,
        FormFactoryInterface $formFactory,
        MessageBusInterface $bus
    ): Response {
        $form = $formFactory->createNamed('', ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (isset($data['user'])) {
                /** @var User $user */
                $user = $data['user'];
                $bus->dispatch(new RequestUserPassword($user->getId()));
            }

            return $responder->respond((new RespondRouteRedirect('home'))->withFlashes([
                'success' => sprintf('Hi %s, we\'ve send you a password reset link.', $data['email']),
            ]));
        }

        return $responder->respond(new RespondTemplate('user/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
