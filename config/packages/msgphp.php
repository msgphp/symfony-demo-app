<?php

use MsgPhp\{Eav, User};
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container->extension('msgphp_eav', [
        'class_mapping' => [
            Eav\Entity\Attribute::class => \App\Entity\Eav\Attribute::class,
            Eav\Entity\AttributeValue::class => \App\Entity\Eav\AttributeValue::class,
        ],
        'default_id_type' => 'uuid',
    ]);

    $container->extension('msgphp_user', [
        'class_mapping' => [
            User\Entity\Role::class => \App\Entity\User\Role::class,
            User\Entity\User::class => \App\Entity\User\User::class,
            User\Entity\UserAttributeValue::class => \App\Entity\User\UserAttributeValue::class,
            User\Entity\UserRole::class => \App\Entity\User\UserRole::class,
            User\Entity\UserEmail::class => \App\Entity\User\UserEmail::class,
        ],
        'default_id_type' => 'uuid',
        'username_lookup' => [
            ['target' => User\Entity\UserEmail::class, 'field' => 'email'],
        ],
    ]);

    $container->services()
        ->alias('msgphp.messenger.event_bus', 'event_bus')
    ;
};
