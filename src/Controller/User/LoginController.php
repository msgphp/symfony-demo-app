<?php

namespace App\Controller\User;

use App\Form\User\LoginType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

final class LoginController
{
    public function __invoke(
        Environment $twig,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        AuthenticationUtils $authenticationUtils
    ): Response
    {
        $form = $formFactory->createNamed('', LoginType::class, [
            'email' => $authenticationUtils->getLastUsername(),
        ]);

        if (null !== $error = $authenticationUtils->getLastAuthenticationError(true)) {
            $form->addError(new FormError($error->getMessage(), $error->getMessageKey(), $error->getMessageData()));
        }

        return new Response($twig->render('user/login.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
