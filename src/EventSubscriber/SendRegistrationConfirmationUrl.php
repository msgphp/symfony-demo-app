<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User\User;
use MsgPhp\User\Event\UserCreatedEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Twig\Environment;

final class SendRegistrationConfirmationUrl implements MessageHandlerInterface
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
            ->setBody($this->twig->render('user/email/confirm_registration.txt.twig', $params), 'text/plain')
            ->addPart($this->twig->render('user/email/confirm_registration.html.twig', $params), 'text/html')
        ;

        $this->mailer->send($message);
    }
}
