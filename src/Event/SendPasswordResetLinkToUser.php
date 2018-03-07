<?php

namespace App\Event;

use App\Entity\User\User;
use MsgPhp\User\Event\UserPasswordRequestedEvent;
use Twig\Environment;

final class SendPasswordResetLinkToUser
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function __invoke(UserPasswordRequestedEvent $event): void
    {
        $this->notify($event->user);
    }

    public function notify(User $user): void
    {
        if (null === $user->getPasswordResetToken()) {
            return;
        }

        $params = ['user' => $user];
        $message = (new \Swift_Message('Reset your password at The App'))
            ->addTo($user->getEmail())
            ->setBody($this->twig->render('User/email/reset_password.txt.twig', $params), 'text/plain')
            ->addPart($this->twig->render('User/email/reset_password.html.twig', $params), 'text/html');

        $this->mailer->send($message);
    }
}
