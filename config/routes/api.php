<?php

use App\Api\Endpoint\MeEndpoint;

use App\Api\Projection\UserProjection;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $endpoints = [
        'api_me' => ['/me', MeEndpoint::class,[
            '_api_resource_class' => UserProjection::class,
            '_api_item_operation_name' => 'get_current',
        ]],
    ];

    $routes
        ->add('api_login_check', '/api/login-check')
    ;

    foreach ($endpoints as $route => $endpoint) {
        [$path, $controller, $defaults] = $endpoint;
        $path = '/api'.$path;
        $defaults += ['_api_receive' => false];

        $routes
            ->add($route, $path)
                ->controller($controller)
                ->defaults($defaults)
            ->add($route.'.formatted', $path.'.{_format}')
                ->controller($controller)
                ->defaults($defaults);
    }
};
