<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use MsgPhp\User\Event\UserCreated;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendRegistrationConfirmationUrl implements MessageHandlerInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(UserCreated $event): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        $this->notify($event->user);
    }

    public function notify(User $user): void
    {
        if ($user->isConfirmed()) {
            return;
        }

        $params = ['user' => $user];
        $message = (new TemplatedEmail())
            ->from('webmaster@localhost')
            ->to($user->getEmail())
            ->subject('Confirm your account at The App')
            ->textTemplate('user/email/confirm_registration.txt.twig')
            ->htmlTemplate('user/email/confirm_registration.html.twig')
            ->context($params)
        ;

        $this->mailer->send($message);
    }
}
