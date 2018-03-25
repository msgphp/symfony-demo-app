<?php

namespace App\Api\Endpoint;

use App\Entity\User\User;
use Symfony\Component\HttpFoundation\Request;

final class MeEndpoint
{
    public function __invoke(Request $request, User $user = null)
    {
        dump($user);die;
    }
}
