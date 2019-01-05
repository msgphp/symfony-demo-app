<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Form\User\ForgotPasswordType;
use App\Http\Responder;
use App\Http\RespondRouteRedirect;
use App\Http\RespondTemplate;
use MsgPhp\User\Command\RequestUserPasswordCommand;
use MsgPhp\User\Repository\UserRepositoryInterface;
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
        MessageBusInterface $bus,
        UserRepositoryInterface $repository
    ): Response {
        $form = $formFactory->createNamed('', ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bus->dispatch(new RequestUserPasswordCommand($repository->findByUsername($email = $form->getData()['email'])->getId()));

            return $responder->respond((new RespondRouteRedirect('home'))->withFlashes([
                'success' => sprintf('Hi %s, we\'ve send you a password reset link.', $email),
            ]));
        }

        return $responder->respond(new RespondTemplate('user/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
