<?php

namespace App\Console;

use App\Entity\User\User;
use MsgPhp\Domain\Infra\Console\ContextBuilder\ContextElement;
use MsgPhp\Domain\Infra\Console\ContextBuilder\ContextElementProviderInterface;
use MsgPhp\User\Password\PasswordHashingInterface;

final class ContextElementProvider implements ContextElementProviderInterface
{
    private $passwordHashing;

    public function __construct(PasswordHashingInterface $passwordHashing)
    {
        $this->passwordHashing = $passwordHashing;
    }

    public function getElement(string $class, string $method, string $argument): ?ContextElement
    {
        if ('email' === $argument) {
            return new ContextElement('E-mail');
        }

        if (User::class === $class && 'password' === $argument) {
            return new ContextElement(
                'Password',
                '',
                function (string $value, array $context) {
                    return $this->passwordHashing->hash($value);
                },
                function () {
                    return bin2hex(random_bytes(8));
                },
                true
            );
        }

        return null;
    }
}
