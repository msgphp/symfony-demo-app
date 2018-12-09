<?php

namespace App\Controller\User;

use App\Form\User\LoginType;
use App\Form\User\OneTimeLoginType;
use App\Http\Responder;
use App\Http\RespondTemplate;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/login", name="login")
 */
final class LoginController
{
    public function __invoke(
        Request $request,
        Responder $responder,
        FormFactoryInterface $formFactory,
        AuthenticationUtils $authenticationUtils
    ): Response
    {
        // one-time login
        $oneTimeLoginForm = $formFactory->createNamed('', OneTimeLoginType::class);

        // regular form login
        $form = $formFactory->createNamed('', LoginType::class, [
            'email' => $authenticationUtils->getLastUsername(),
        ]);

        if (null !== $error = $authenticationUtils->getLastAuthenticationError(true)) {
            $form->addError(new FormError($error->getMessage(), $error->getMessageKey(), $error->getMessageData()));
        }

        return $responder->respond(new RespondTemplate('user/login.html.twig', [
            'form' => $form->createView(),
            'oneTimeLoginForm' => $oneTimeLoginForm->createView(),
        ]));
    }
}
