<?php

namespace App\Api\Endpoint;

use App\Entity\User\User;

final class MeEndpoint
{
    public function __invoke(User $user)
    {
        return null === $user ? null : $user->getEmail();
    }
}
