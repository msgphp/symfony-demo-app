<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use MsgPhp\User\Event\UserPasswordRequested;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendPasswordResetUrl implements MessageHandlerInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
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
        $message = (new TemplatedEmail())
            ->from('webmaster@localhost')
            ->to($user->getEmail())
            ->subject('Reset your password at The App')
            ->textTemplate('user/email/reset_password.txt.twig')
            ->htmlTemplate('user/email/reset_password.html.twig')
            ->context($params)
        ;

        $this->mailer->send($message);
    }
}
