<?php

use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    // force webserver independent routing
    $routes
        ->add('admin', '/admin')
            ->controller(RedirectController::class.'::urlRedirectAction')
            ->defaults([
                'path' => '/admin/index.html',
                'permanent' => true,
                'keepQueryParams' => true,
            ])
    ;
};
