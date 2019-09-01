<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use MsgPhp\User\Event\UserPasswordRequested;
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

    public function __invoke(UserPasswordRequested $event): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
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
