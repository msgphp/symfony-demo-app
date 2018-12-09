<?php

namespace App\Controller\User;

use App\Form\User\RegisterType;
use App\Http\Responder;
use App\Http\RespondRouteRedirect;
use App\Http\RespondTemplate;
use MsgPhp\User\Command\CreateUserCommand;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/register", name="register")
 */
final class RegisterController
{
    public function __invoke(
        Request $request,
        Responder $responder,
        FormFactoryInterface $formFactory,
        MessageBusInterface $bus
    ): Response
    {
        $form = $formFactory->createNamed('', RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bus->dispatch(new CreateUserCommand($data = $form->getData()));

            return $responder->respond((new RespondRouteRedirect('home'))->withFlashes([
                'success' => sprintf('Hi %s, you\'re successfully registered. We\'ve send you a confirmation link.', $data['email']),
            ]));
        }

        return $responder->respond(new RespondTemplate('user/register.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
