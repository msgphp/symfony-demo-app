<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->import('.', 'api_platform')
        ->prefix('/api')
    ;

    $routes
        ->add('api_login', '/api/login')
            ->methods(['POST'])
    ;
};
