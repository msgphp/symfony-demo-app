<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\UserEmail;
use MsgPhp\User\Event\UserEmailAdded;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Twig\Environment;

final class SendEmailConfirmationUrl implements MessageHandlerInterface
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function __invoke(UserEmailAdded $event): void
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
            ->addPart($this->twig->render('user/email/confirm_email.html.twig', $params), 'text/html')
        ;

        $this->mailer->send($message);
    }
}
