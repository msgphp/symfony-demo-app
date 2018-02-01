<?php

namespace App\Controller\User;

use App\Form\User\RegisterType;
use MsgPhp\Domain\Message\DomainMessageBusInterface;
use MsgPhp\User\Command\CreateUserCommand;
use MsgPhp\User\Command\EnableUserCommand;
use MsgPhp\User\Password\PasswordHashingInterface;
use MsgPhp\User\Repository\UserRepositoryInterface;
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
        DomainMessageBusInterface $bus,
        PasswordHashingInterface $passwordHashing,
        UserRepositoryInterface $repository
    ): Response
    {
        $form = $formFactory->createNamed('', RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['password'] = $passwordHashing->hash($data['password']);

            $bus->dispatch(new CreateUserCommand($data));
            $bus->dispatch(new EnableUserCommand($repository->findByUsername($data['email'])->getId()));
            $flashBag->add('success', sprintf('Hi %s, you\'re successfully registered. You can now login.', $data['email']));

            return new RedirectResponse($urlGenerator->generate('login'));
        }

        return new Response($twig->render('User/register.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
