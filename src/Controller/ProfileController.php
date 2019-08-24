<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserEmail;
use App\EventSubscriber\SendEmailConfirmationUrl;
use App\Form\AddEmailType;
use App\Form\ChangePasswordType;
use App\Http\Responder;
use App\Http\RespondNotFound;
use App\Http\RespondRouteRedirect;
use App\Http\RespondTemplate;
use App\Security\PasswordConfirmation;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use MsgPhp\User\Command\AddUserEmail;
use MsgPhp\User\Command\ChangeUserCredential;
use MsgPhp\User\Command\DeleteUserEmail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
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
            $bus->dispatch(new AddUserEmail($user->getId(), $email = $emailForm->getData()['email']));

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
            $bus->dispatch(new DeleteUserEmail($primaryEmail));
            $bus->dispatch(new ChangeUserCredential($user->getId(), ['email' => $primaryEmail]));
            $bus->dispatch(new AddUserEmail($user->getId(), $currentEmail, ['confirm' => true]));

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

            $bus->dispatch(new DeleteUserEmail($deleteEmail));

            return $responder->respond((new RespondRouteRedirect('profile'))->withFlashes([
                'success' => sprintf('E-mail %s deleted.', $deleteEmail),
            ]));
        }

        // change password
        $passwordForm = $formFactory->create(ChangePasswordType::class);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $bus->dispatch(new ChangeUserCredential($user->getId(), $passwordForm->getData()));

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
