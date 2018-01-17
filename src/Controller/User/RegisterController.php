<?php

namespace App\Controller\User;

use App\Form\User\RegisterType;
use MsgPhp\Domain\CommandBusInterface;
use MsgPhp\User\Command\CreatePendingUserCommand;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class RegisterController
{
    public function __invoke(
        Request $request,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig,
        CommandBusInterface $commandBus
    ): Response
    {
        $form = $formFactory->createNamed('', RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $commandBus->handle(new CreatePendingUserCommand($data['email'], $data['password']));
            $flashBag->add('success', sprintf('Hi %s, we\'ve send you a confirmation link.', $data['email']));

            return new RedirectResponse($urlGenerator->generate('index'));
        }

        return new Response($twig->render('User/register.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
