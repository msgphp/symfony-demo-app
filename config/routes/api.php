<?php

use App\Controller;

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes
        ->add('api_index', '/api/test-connection')
            ->controller(Controller\Api\DefaultController::class)
        ->add('api_login_check', '/api/login_check')
    ;
};
