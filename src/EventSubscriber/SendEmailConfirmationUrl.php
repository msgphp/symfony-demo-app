<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\UserEmail;
use MsgPhp\User\Event\UserEmailAdded;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendEmailConfirmationUrl implements MessageHandlerInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(UserEmailAdded $event): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        $this->notify($event->userEmail);
    }

    public function notify(UserEmail $userEmail): void
    {
        if ($userEmail->isConfirmed()) {
            return;
        }

        $params = ['userEmail' => $userEmail];
        $message = (new TemplatedEmail())
            ->from('webmaster@localhost')
            ->to($userEmail->getEmail())
            ->subject('Confirm your e-mail at The App')
            ->textTemplate('user/email/confirm_email.txt.twig')
            ->htmlTemplate('user/email/confirm_email.html.twig')
            ->context($params)
        ;

        $this->mailer->send($message);
    }
}
