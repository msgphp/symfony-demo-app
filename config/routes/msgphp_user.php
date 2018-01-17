<?php

use App\Controller\User\{ConfirmAccountController, ConfirmEmailController, ForgotPasswordController, LoginController, MyAccountController, RegisterController, ResetPasswordController};
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes
        ->add('login', '/login')
            ->controller(LoginController::class)
        ->add('logout', '/logout')
    ;
};
