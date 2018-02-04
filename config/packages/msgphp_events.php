<?php

use App\Event\EnableConfirmedUser;
use App\Event\SendConfirmationLinkToCreatedUser;
use MsgPhp\User\Event\UserConfirmedEvent;
use MsgPhp\User\Event\UserCreatedEvent;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()
            ->autowire()
            ->public()

        ->set(SendConfirmationLinkToCreatedUser::class)
            ->tag('event_subscriber', ['subscribes_to' => UserCreatedEvent::class])
        ->set(EnableConfirmedUser::class)
            ->tag('event_subscriber', ['subscribes_to' => UserConfirmedEvent::class])
    ;
};
