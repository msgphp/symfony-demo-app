<?php

declare(strict_types=1);

use MsgPhp\Eav\Attribute;
use MsgPhp\Eav\AttributeValue;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('msgphp_eav', [
        'class_mapping' => [
            Attribute::class => \App\Entity\Attribute::class,
            AttributeValue::class => \App\Entity\AttributeValue::class,
        ],
        'default_id_type' => 'uuid',
    ]);
};
