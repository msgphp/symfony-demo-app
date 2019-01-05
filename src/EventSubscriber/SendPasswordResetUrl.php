<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User\User;
use MsgPhp\User\Event\UserPasswordRequestedEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Twig\Environment;

final class SendPasswordResetUrl implements MessageHandlerInterface
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
            ->setBody($this->twig->render('user/email/reset_password.txt.twig', $params), 'text/plain')
            ->addPart($this->twig->render('user/email/reset_password.html.twig', $params), 'text/html')
        ;

        $this->mailer->send($message);
    }
}
