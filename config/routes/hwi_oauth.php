<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->import('@HWIOAuthBundle/Resources/config/routing/redirect.xml')
        ->prefix('/oauth/redirect')
    ;
    $routes->import('@HWIOAuthBundle/Resources/config/routing/connect.xml')
        ->prefix('/oauth/connect')
    ;
    $routes->import('@HWIOAuthBundle/Resources/config/routing/login.xml')
        ->prefix('/oauth/login')
    ;

    $routes
        ->add('oauth_google_check', '/oauth/login/check-google')
    ;
};
