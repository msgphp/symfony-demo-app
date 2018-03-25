<?php

namespace App\Api\Endpoint;

use Symfony\Component\HttpFoundation\Request;

final class MeEndpoint
{
    public function __invoke(Request $request)
    {
        $routeParams = $request->attributes->get('_route_params', []);
        $routeParams['id'] = 'wDCavjbK3NCWnhVkALmc8';

        $request->attributes->set('_route_params', $routeParams);
        $request->attributes->set('id', $routeParams['id']);

        return [1, 2, 3];
    }
}
