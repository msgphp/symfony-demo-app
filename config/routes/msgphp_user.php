<?php

use App\Controller;

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes
        ->add('login', '/login')
            ->controller(Controller\User\LoginController::class)
        ->add('logout', '/logout')

         ->add('register', '/register')
            ->controller(Controller\User\RegisterController::class)
    ;
};
