<?php

namespace App\Event;

use App\Entity\User\User;
use MsgPhp\User\Event\UserCreatedEvent;
use Twig\Environment;

final class SendConfirmationLinkToCreatedUser
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function __invoke(UserCreatedEvent $event): void
    {
        $this->notify($event->user);
    }

    public function notify(User $user): void
    {
        if ($user->isConfirmed()) {
            return;
        }

        $params = ['user' => $user];
        $message = (new \Swift_Message('Confirm your account at The App'))
            ->addTo($user->getEmail())
            ->setBody($this->twig->render('User/email/confirm_registration.txt.twig', $params), 'plain/text')
            ->addPart($this->twig->render('User/email/confirm_registration.html.twig', $params), 'text/html');

        $this->mailer->send($message);
    }
}
