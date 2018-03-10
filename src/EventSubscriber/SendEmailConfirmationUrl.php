<?php

namespace App\EventSubscriber;

use App\Entity\User\UserEmail;
use MsgPhp\User\Event\UserEmailCreatedEvent;
use Twig\Environment;

final class SendEmailConfirmationUrl
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function __invoke(UserEmailCreatedEvent $event): void
    {
        $this->notify($event->userEmail);
    }

    public function notify(UserEmail $userEmail): void
    {
        if ($userEmail->isConfirmed()) {
            return;
        }

        $params = ['userEmail' => $userEmail];
        $message = (new \Swift_Message('Confirm your e-mail at The App'))
            ->addTo($userEmail->getEmail())
            ->setBody($this->twig->render('user/email/confirm_email.txt.twig', $params), 'text/plain')
            ->addPart($this->twig->render('user/email/confirm_email.html.twig', $params), 'text/html');

        $this->mailer->send($message);
    }
}
