<?php

namespace App\Controller\Api;

use App\Entity\User\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

final class DefaultController
{
    /**
     * @ParamConverter("user", converter="msgphp.current_user")
     */
    public function __invoke(User $user): Response
    {
        return new Response("User {$user->getId()} authenticated !");
    }
}
