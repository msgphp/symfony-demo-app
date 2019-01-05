<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User\User;
use App\Entity\User\UserEmail;
use App\EventSubscriber\SendEmailConfirmationUrl;
use App\Form\User\AddEmailType;
use App\Form\User\ChangePasswordType;
use App\Http\Responder;
use App\Http\RespondNotFound;
use App\Http\RespondRouteRedirect;
use App\Http\RespondTemplate;
use App\Security\PasswordConfirmation;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use MsgPhp\User\Command\AddUserEmailCommand;
use MsgPhp\User\Command\ChangeUserCredentialCommand;
use MsgPhp\User\Command\DeleteUserEmailCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/profile", name="profile")
 */
final class ProfileController
{
    /**
     * @ParamConverter("user", converter="msgphp.current_user")
     */
    public function __invoke(
        User $user,
        Request $request,
        Responder $responder,
        JWTTokenManagerInterface $jwtTokenManager,
        FormFactoryInterface $formFactory,
        TokenStorageInterface $tokenStorage,
        MessageBusInterface $bus,
        PasswordConfirmation $passwordConfirmation,
        SendEmailConfirmationUrl $sendEmailConfirmationUrl,
        UserInterface $securityUser
    ): Response {
        // generate JWT token
        if ($request->query->getBoolean('generate-jwt')) {
            return $responder->respond((new RespondRouteRedirect('profile'))->withFlashes([
                'success' => sprintf('Generated JWT token: %s', $jwtTokenManager->create($securityUser)),
            ]));
        }

        // add email
        $emailForm = $formFactory->create(AddEmailType::class);
        $emailForm->handleRequest($request);

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $bus->dispatch(new AddUserEmailCommand($user->getId(), $email = $emailForm->getData()['email']));

            return $responder->respond((new RespondRouteRedirect('profile'))->withFlashes([
                'success' => sprintf('E-mail %s added. We\'ve send you a confirmation link.', $email),
            ]));
        }

        // mark primary email
        if ($primaryEmail = $request->query->get('primary-email')) {
            /** @var UserEmail $userEmail */
            if (!($userEmail = $user->getEmails()->get($primaryEmail)) || !$userEmail->isConfirmed()) {
                return $responder->respond(new RespondNotFound());
            }

            $confirmResponse = $passwordConfirmation->confirm($request);

            if (null !== $confirmResponse) {
                return $confirmResponse;
            }

            $currentEmail = $user->getEmail();
            $bus->dispatch(new DeleteUserEmailCommand($primaryEmail));
            $bus->dispatch(new ChangeUserCredentialCommand($user->getId(), ['email' => $primaryEmail]));
            $bus->dispatch(new AddUserEmailCommand($user->getId(), $currentEmail, ['confirm' => true]));

            return $responder->respond((new RespondRouteRedirect('profile'))->withFlashes([
                'success' => sprintf('E-mail %s marked primary.', $primaryEmail),
            ]));
        }

        // send email confirmation link
        if ($confirmEmail = $request->query->get('confirm-email')) {
            /** @var UserEmail $userEmail */
            if (!($userEmail = $user->getEmails()->get($confirmEmail)) || $userEmail->isConfirmed()) {
                return $responder->respond(new RespondNotFound());
            }

            $sendEmailConfirmationUrl->notify($userEmail);

            return $responder->respond((new RespondRouteRedirect('profile'))->withFlashes([
                'success' => 'We\'ve send you a e-mail confirmation link.',
            ]));
        }

        // delete email
        if ($deleteEmail = $request->query->get('delete-email')) {
            if (!$user->getEmails()->containsKey($deleteEmail)) {
                return $responder->respond(new RespondNotFound());
            }

            $confirmResponse = $passwordConfirmation->confirm($request);

            if (null !== $confirmResponse) {
                return $confirmResponse;
            }

            $bus->dispatch(new DeleteUserEmailCommand($deleteEmail));

            return $responder->respond((new RespondRouteRedirect('profile'))->withFlashes([
                'success' => sprintf('E-mail %s deleted.', $deleteEmail),
            ]));
        }

        // change password
        $passwordForm = $formFactory->create(ChangePasswordType::class);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $bus->dispatch(new ChangeUserCredentialCommand($user->getId(), ['password' => $passwordForm->getData()['password']]));

            return $responder->respond((new RespondRouteRedirect('profile'))->withFlashes([
                'success' => 'Your password is now changed.',
            ]));
        }

        // render view
        return $responder->respond(new RespondTemplate('user/profile.html.twig', [
            'email_form' => $emailForm->createView(),
            'password_form' => $passwordForm->createView(),
        ]));
    }
}
