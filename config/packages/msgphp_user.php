<?php

declare(strict_types=1);

use MsgPhp\User\Role;
use MsgPhp\User\User;
use MsgPhp\User\UserAttributeValue;
use MsgPhp\User\UserEmail;
use MsgPhp\User\Username;
use MsgPhp\User\UserRole;
use MsgPhp\User\Role\UserRoleProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    $container->extension('msgphp_user', [
        'class_mapping' => [
            Role::class => \App\Entity\User\Role::class,
            User::class => \App\Entity\User\User::class,
            UserAttributeValue::class => \App\Entity\User\UserAttributeValue::class,
            UserEmail::class => \App\Entity\User\UserEmail::class,
            Username::class => \App\Entity\User\Username::class,
            UserRole::class => \App\Entity\User\UserRole::class,
        ],
        'default_id_type' => 'uuid',
        'username_lookup' => [
            ['target' => \App\Entity\User\UserEmail::class, 'field' => 'email', 'mapped_by' => 'user'],
        ],
        'role_providers' => [
            'default' => [\App\Security\RoleProvider::ROLE_USER],
            UserRoleProvider::class,
            \App\Security\RoleProvider::class,
        ],
    ]);
};
