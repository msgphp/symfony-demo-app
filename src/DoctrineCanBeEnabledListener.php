<?php

namespace App;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use MsgPhp\Domain\Entity\Features\CanBeEnabled;

class DoctrineCanBeEnabledListener
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        $metadata = $event->getClassMetadata();

        if (in_array(CanBeEnabled::class, $metadata->getReflectionClass()->getTraitNames(), true)) {
            if ($metadata->hasField('enabled')) {
                return;
            }

            $metadata->mapField([
                'fieldName' => 'enabled',
                'type' => 'boolean',
            ]);
        }
    }
}
