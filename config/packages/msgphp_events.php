<?php

use App\Event\ClearPasswordResetToken;
use App\Event\EnableConfirmedUser;
use App\Event\SendConfirmationLinkToCreatedUser;
use App\Event\SendPasswordResetLinkToUser;
use MsgPhp\User\Event\UserConfirmedEvent;
use MsgPhp\User\Event\UserCreatedEvent;
use MsgPhp\User\Event\UserCredentialChangedEvent;
use MsgPhp\User\Event\UserPasswordRequestedEvent;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()
            ->autowire()
            ->public()

        ->set(ClearPasswordResetToken::class)
            ->tag('event_subscriber', ['subscribes_to' => UserCredentialChangedEvent::class])
        ->set(EnableConfirmedUser::class)
            ->tag('event_subscriber', ['subscribes_to' => UserConfirmedEvent::class])
        ->set(SendConfirmationLinkToCreatedUser::class)
            ->tag('event_subscriber', ['subscribes_to' => UserCreatedEvent::class])
        ->set(SendPasswordResetLinkToUser::class)
            ->tag('event_subscriber', ['subscribes_to' => UserPasswordRequestedEvent::class])
    ;
};
