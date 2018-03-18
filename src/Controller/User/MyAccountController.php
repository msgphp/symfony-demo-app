<?php

namespace App\Controller\User;

use App\Entity\Eav\Attribute;
use App\Entity\User\User;
use App\Entity\User\UserEmail;
use App\EventSubscriber\SendEmailConfirmationUrl;
use App\Form\User\AddEmailType;
use App\Form\User\ChangePasswordType;
use App\Security\PasswordConfirmation;
use MsgPhp\Domain\Factory\EntityAwareFactoryInterface;
use MsgPhp\User\Command\ChangeUserCredentialCommand;
use MsgPhp\User\Command\AddUserEmailCommand;
use MsgPhp\User\Command\DeleteUserEmailCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

final class MyAccountController
{
    /**
     * @ParamConverter("user", converter="msgphp.current_user")
     */
    public function __invoke(
        User $user,
        Request $request,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig,
        TokenStorageInterface $tokenStorage,
        CommandBus $bus,
        PasswordConfirmation $passwordConfirmation,
        SendEmailConfirmationUrl $sendEmailConfirmationUrl,
        EntityAwareFactoryInterface $factory
    ): Response
    {
        // add email
        $emailForm = $formFactory->create(AddEmailType::class);
        $emailForm->handleRequest($request);

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $bus->handle(new AddUserEmailCommand($user->getId(), $email = $emailForm->getData()['email']));
            $flashBag->add('success', sprintf('E-mail %s added. We\'ve send you a confirmation link.', $email));

            return new RedirectResponse($urlGenerator->generate('my_account'));
        }

        // mark primary email
        if ($primaryEmail = $request->query->get('primary-email')) {
            /** @var UserEmail $userEmail */
            if (!($userEmail = $user->getEmails()->get($primaryEmail)) || !$userEmail->isConfirmed()) {
                throw new NotFoundHttpException();
            }

            if (null !== $confirmResponse = $passwordConfirmation->confirm($request)) {
                return $confirmResponse;
            }

            $currentEmail = $user->getEmail();
            $bus->handle(new DeleteUserEmailCommand($primaryEmail));
            $bus->handle(new ChangeUserCredentialCommand($user->getId(), ['email' => $primaryEmail]));
            $bus->handle(new AddUserEmailCommand($user->getId(), $currentEmail, ['confirm' => true]));
            $flashBag->add('success', sprintf('E-mail %s marked primary.', $primaryEmail));

            return new RedirectResponse($urlGenerator->generate('my_account'));
        }

        // delete email
        if ($deleteEmail = $request->query->get('delete-email')) {
            if (!$user->getEmails()->containsKey($deleteEmail)) {
                throw new NotFoundHttpException();
            }

            if (null !== $confirmResponse = $passwordConfirmation->confirm($request)) {
                return $confirmResponse;
            }

            $bus->handle(new DeleteUserEmailCommand($deleteEmail));
            $flashBag->add('success', sprintf('E-mail %s deleted.', $deleteEmail));

            return new RedirectResponse($urlGenerator->generate('my_account'));
        }

        // change password
        $passwordForm = $formFactory->create(ChangePasswordType::class);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $bus->handle(new ChangeUserCredentialCommand($user->getId(), [
                'password' => $passwordForm->getData()['password'],
            ]));
            $flashBag->add('success', 'Your password is now changed.');

            return new RedirectResponse($urlGenerator->generate('my_account'));
        }

        // send email confirmation link
        if ($confirmEmail = $request->query->get('confirm-email')) {
            /** @var UserEmail $userEmail */
            if (!($userEmail = $user->getEmails()->get($confirmEmail)) || $userEmail->isConfirmed()) {
                throw new NotFoundHttpException();
            }

            $sendEmailConfirmationUrl->notify($userEmail);
            $flashBag->add('success', 'We\'ve send you a e-mail confirmation link.');

            return new RedirectResponse($urlGenerator->generate('my_account'));
        }

        // render view
        return new Response($twig->render('user/my_account.html.twig', [
            'email_form' => $emailForm->createView(),
            'password_form' => $passwordForm->createView(),
        ]));
    }
}
