<?php

namespace App\Controller\User;

use App\Entity\User\User;
use App\Entity\User\UserEmail;
use App\EventSubscriber\SendEmailConfirmationUrl;
use App\Form\User\AddEmailType;
use App\Form\User\ChangePasswordType;
use App\Security\PasswordConfirmation;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use MsgPhp\Domain\Factory\EntityAwareFactoryInterface;
use MsgPhp\User\Command\ChangeUserCredentialCommand;
use MsgPhp\User\Command\AddUserEmailCommand;
use MsgPhp\User\Command\DeleteUserEmailCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

final class MyAccountController
{
    /**
     * @ParamConverter("user", converter="msgphp.current_user")
     */
    public function __invoke(
        User $user,
        Request $request,
        JWTTokenManagerInterface $jwtTokenManager,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig,
        TokenStorageInterface $tokenStorage,
        MessageBusInterface $bus,
        PasswordConfirmation $passwordConfirmation,
        SendEmailConfirmationUrl $sendEmailConfirmationUrl,
        EntityAwareFactoryInterface $factory,
        UserInterface $securityUser
    ): Response
    {
        // generate JWT token
        if ($request->query->getBoolean('generate-jwt')) {
            $flashBag->add('success', sprintf('Generated JWT token: %s', $jwtTokenManager->create($securityUser)));

            return new RedirectResponse($urlGenerator->generate('my_account'));
        }

        // add email
        $emailForm = $formFactory->create(AddEmailType::class);
        $emailForm->handleRequest($request);

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $bus->dispatch(new AddUserEmailCommand($user->getId(), $email = $emailForm->getData()['email']));
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
            $bus->dispatch(new DeleteUserEmailCommand($primaryEmail));
            $bus->dispatch(new ChangeUserCredentialCommand($user->getId(), ['email' => $primaryEmail]));
            $bus->dispatch(new AddUserEmailCommand($user->getId(), $currentEmail, ['confirm' => true]));
            $flashBag->add('success', sprintf('E-mail %s marked primary.', $primaryEmail));

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

        // delete email
        if ($deleteEmail = $request->query->get('delete-email')) {
            if (!$user->getEmails()->containsKey($deleteEmail)) {
                throw new NotFoundHttpException();
            }

            if (null !== $confirmResponse = $passwordConfirmation->confirm($request)) {
                return $confirmResponse;
            }

            $bus->dispatch(new DeleteUserEmailCommand($deleteEmail));
            $flashBag->add('success', sprintf('E-mail %s deleted.', $deleteEmail));

            return new RedirectResponse($urlGenerator->generate('my_account'));
        }

        // change password
        $passwordForm = $formFactory->create(ChangePasswordType::class);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $bus->dispatch(new ChangeUserCredentialCommand($user->getId(), ['password' => $passwordForm->getData()['password']]));
            $flashBag->add('success', 'Your password is now changed.');

            return new RedirectResponse($urlGenerator->generate('my_account'));
        }

        // render view
        return new Response($twig->render('user/my_account.html.twig', [
            'email_form' => $emailForm->createView(),
            'password_form' => $passwordForm->createView(),
        ]));
    }
}
