<?php

namespace App\Controller\User;

use App\Form\User\LoginType;
use App\Form\User\OneTimeLoginType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

final class LoginController
{
    public function __invoke(
        Request $request,
        Environment $twig,
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

        return new Response($twig->render('user/login.html.twig', [
            'form' => $form->createView(),
            'oneTimeLoginForm' => $oneTimeLoginForm->createView(),
        ]));
    }
}
