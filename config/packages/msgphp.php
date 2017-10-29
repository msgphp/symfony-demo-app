<?php

use MsgPhp\Eav\Entity\{Attribute, AttributeValue};
use MsgPhp\User\Entity\{PendingUser, User, UserAttributeValue, UserRole, UserSecondaryEmail};
use MsgPhp\Eav\Infra\Doctrine\Type\{AttributeIdType, AttributeValueIdType};
use MsgPhp\User\Infra\Doctrine\Type\UserIdType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container->extension('msgphp_eav', [
        'class_mapping' => $classMappingEav = [
            Attribute::class => \App\Entity\Eav\Attribute::class,
            AttributeValue::class => \App\Entity\Eav\AttributeValue::class,
        ],
    ]);

    $container->extension('msgphp_user', [
        'class_mapping' => $classMappingUser = [
            PendingUser::class => \App\Entity\User\PendingUser::class,
            User::class => \App\Entity\User\User::class,
            UserAttributeValue::class => \App\Entity\User\UserAttributeValue::class,
            UserRole::class => \App\Entity\User\UserRole::class,
            UserSecondaryEmail::class => \App\Entity\User\UserSecondaryEmail::class,
        ],
    ]);

    $container->extension('doctrine', [
        'dbal' => [
            'types' => [
                AttributeIdType::NAME => AttributeIdType::class,
                AttributeValueIdType::NAME => AttributeValueIdType::class,
                UserIdType::NAME => UserIdType::class,
            ],
        ],
        'orm' => [
            'resolve_target_entities' => $classMappingEav + $classMappingUser,
            'mappings' => [
                'msgphp_attribute' => [
                    'dir' => '%kernel.project_dir%/vendor/msgphp/eav/Infra/Doctrine/Resources/mapping',
                    'type' => 'xml',
                    'prefix' => 'MsgPhp\Eav\Entity',
                    'is_bundle' => false,
                ],
                'msgphp_user' => [
                    'dir' => '%kernel.project_dir%/vendor/msgphp/user/Infra/Doctrine/Resources/mapping',
                    'type' => 'xml',
                    'prefix' => 'MsgPhp\User\Entity',
                    'is_bundle' => false,
                ],
            ],
        ],
    ]);
};
