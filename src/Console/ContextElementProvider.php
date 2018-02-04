<?php

namespace App\Console;

use App\Entity\User\User;
use MsgPhp\Domain\Infra\Console\ContextBuilder\ContextElement;
use MsgPhp\Domain\Infra\Console\ContextBuilder\ContextElementProviderInterface;
use MsgPhp\User\Entity\Credential\EmailPassword;
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
        switch ($argument) {
            case 'email':
                return new ContextElement('E-mail');
            case 'password':
                switch ($class) {
                    case User::class:
                    case EmailPassword::class:
                        return new ContextElement(
                            'Password',
                            '',
                            function (string $value) {
                                return $this->passwordHashing->hash($value);
                            },
                            function () {
                                return bin2hex(random_bytes(8));
                            },
                            true
                        );
                }
        }

        return null;
    }
}
