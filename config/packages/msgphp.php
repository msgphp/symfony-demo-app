<?php

use MsgPhp\{Eav, User};
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container->parameters()
        ->set('msgphp.default_data_type', 'uuid');

    $container->extension('msgphp_eav', [
        'class_mapping' => [
            Eav\Entity\Attribute::class => \App\Entity\Eav\Attribute::class,
            Eav\Entity\AttributeValue::class => \App\Entity\Eav\AttributeValue::class,
        ],
    ]);

    $container->extension('msgphp_user', [
        'class_mapping' => [
            User\Entity\User::class => \App\Entity\User\User::class,
            User\Entity\UserAttributeValue::class => \App\Entity\User\UserAttributeValue::class,
            User\Entity\UserRole::class => \App\Entity\User\UserRole::class,
            User\Entity\UserSecondaryEmail::class => \App\Entity\User\UserSecondaryEmail::class,
        ],
        'username_lookup' => [
            ['target' => User\Entity\UserSecondaryEmail::class, 'field' => 'email'],
        ],
    ]);
};
