<?php

namespace App\Entity\Eav;

use Doctrine\ORM\Mapping as ORM;
use MsgPhp\Eav\Entity\Attribute as BaseAttribute;
use MsgPhp\Eav\Infra\Uuid\AttributeId;

/**
 * @ORM\Entity()
 *
 * @final
 */
class Attribute extends BaseAttribute
{
    private const OAUTH_GOOGLE_ID = '9bbddea5-5c02-428b-a1ee-2ff51b271351';

    public static function getOauthId(string $resourceOwner): AttributeId
    {
        switch ($resourceOwner) {
            case 'google':
                return AttributeId::fromValue(self::OAUTH_GOOGLE_ID);
            default:
                throw new \LogicException(sprintf('Unknown oauth resource owner "%s".', $resourceOwner));
        }
    }
}
