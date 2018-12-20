<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Responder;
use App\Http\RespondTemplate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="home")
 */
final class HomeController
{
    public function __invoke(Responder $responder): Response
    {
        return $responder->respond(new RespondTemplate('home.html.twig'));
    }
}
