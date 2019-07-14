<?php

declare(strict_types=1);

use MsgPhp\Eav;
use MsgPhp\User;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('msgphp_eav', [
        'class_mapping' => [
            Eav\Attribute::class => \App\Entity\Eav\Attribute::class,
            Eav\AttributeValue::class => \App\Entity\Eav\AttributeValue::class,
        ],
        'default_id_type' => 'uuid',
    ]);

    $container->extension('msgphp_user', [
        'class_mapping' => [
            User\Role::class => \App\Entity\User\Role::class,
            User\User::class => \App\Entity\User\User::class,
            User\UserAttributeValue::class => \App\Entity\User\UserAttributeValue::class,
            User\UserEmail::class => \App\Entity\User\UserEmail::class,
            User\Username::class => \App\Entity\User\Username::class,
            User\UserRole::class => \App\Entity\User\UserRole::class,
        ],
        'default_id_type' => 'uuid',
        'username_lookup' => [
            ['target' => \App\Entity\User\UserEmail::class, 'field' => 'email', 'mapped_by' => 'user'],
        ],
        'role_providers' => [
            'default' => [\App\Security\RoleProvider::ROLE_USER],
            User\Role\UserRoleProvider::class,
            \App\Security\RoleProvider::class,
        ],
    ]);

    $container->parameters()
        ->set('msgphp.doctrine.mapping_config', [
            'key_max_length' => 191,
        ])
    ;

    $container->services()
        ->alias('msgphp.messenger.event_bus', 'event_bus')
    ;
};
