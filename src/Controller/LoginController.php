<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\LoginType;
use App\Form\OneTimeLoginType;
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
    ): Response {
        $lastAuthError = $authenticationUtils->getLastAuthenticationError(true);

        // one-time login
        $oneTimeLoginForm = $formFactory->createNamed('', OneTimeLoginType::class);

        // regular form login
        $form = $formFactory->createNamed('', LoginType::class, [
            'email' => $authenticationUtils->getLastUsername(),
        ]);

        if (null !== $lastAuthError) {
            $form->addError(new FormError($lastAuthError->getMessage(), $lastAuthError->getMessageKey(), $lastAuthError->getMessageData()));
        }

        return $responder->respond(new RespondTemplate('user/login.html.twig', [
            'form' => $form->createView(),
            'oneTimeLoginForm' => $oneTimeLoginForm->createView(),
        ]));
    }
}
