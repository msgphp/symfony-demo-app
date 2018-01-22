<?php

use MsgPhp\Eav\Entity as Eav;
use MsgPhp\User\Entity as User;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container->parameters()
        ->set('msgphp.default_data_type', 'uuid');

    $container->extension('msgphp_eav', [
        'class_mapping' => [
            Eav\Attribute::class => \App\Entity\Eav\Attribute::class,
            Eav\AttributeValue::class => \App\Entity\Eav\AttributeValue::class,
        ],
    ]);

    $container->extension('msgphp_user', [
        'class_mapping' => [
            User\User::class => \App\Entity\User\User::class,
            User\UserAttributeValue::class => \App\Entity\User\UserAttributeValue::class,
            User\UserRole::class => \App\Entity\User\UserRole::class,
            User\UserSecondaryEmail::class => \App\Entity\User\UserSecondaryEmail::class,
        ],
        'username_lookup' => [
            ['target' => User\UserSecondaryEmail::class, 'field' => 'email'],
        ],
    ]);
};
