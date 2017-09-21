<?php

namespace App\Controller\User;

use App\Form\User\ResetPasswordType;
use MsgPhp\Domain\CommandBusInterface;
use MsgPhp\User\Command\ResetUserPasswordCommand;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class ResetPasswordController
{
    public function __invoke(
        string $token,
        Request $request,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig,
        CommandBusInterface $commandBus
    ): Response
    {
        $form = $formFactory->createNamed('', ResetPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $commandBus->handle(new ResetUserPasswordCommand($token, $data['password']));
            $flashBag->add('success', 'Hi, we\'ve reset your password.');

            return new RedirectResponse($urlGenerator->generate('index'));
        }

        return new Response($twig->render('User/reset_password.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
