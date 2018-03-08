<?php

namespace App\Controller;

use App\Entity\User\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class DefaultController
{
    /**
     * @ParamConverter("user", converter="msgphp.current_user")
     */
    public function __invoke(?User $user, Environment $twig): Response
    {
        return new Response($twig->render('default.html.twig', [
            'current_user' => $user,
        ]));
    }
}
