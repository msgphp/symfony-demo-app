<?php

namespace App\Controller\User;

use App\Entity\User\User;
use App\Form\User\ResetPasswordType;
use MsgPhp\Domain\Message\DomainMessageBusInterface;
use MsgPhp\User\Command\ChangeUserCredentialCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class ResetPasswordController
{
    /**
     * @ParamConverter("user", converter="doctrine.orm", class="App:User\User", options={"mapping": {"token": "passwordResetToken"}})
     */
    public function __invoke(
        Request $request,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig,
        DomainMessageBusInterface $bus,
        User $user
    ): Response
    {
        $form = $formFactory->createNamed('', ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bus->dispatch(new ChangeUserCredentialCommand($user->getId(), ['password' => $form->getData()['password']]));
            $flashBag->add('success', sprintf('Hi %s, we\'ve reset your password.', $user->getEmail()));

            return new RedirectResponse($urlGenerator->generate('index'));
        }

        return new Response($twig->render('User/reset_password.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
