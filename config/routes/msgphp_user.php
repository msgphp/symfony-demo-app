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
        ->add('register_confirm', '/register/confirm/{token}')
            ->controller(Controller\User\ConfirmRegistrationController::class)
        ->add('forgot_password', '/forgot-password')
            ->controller(Controller\User\ForgotPasswordController::class)
        ->add('reset_password', '/reset-password/{token}')
            ->controller(Controller\User\ResetPasswordController::class)
    ;
};
