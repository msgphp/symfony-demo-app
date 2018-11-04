<?php

namespace App\Entity\Eav;

use Doctrine\ORM\Mapping as ORM;
use MsgPhp\Eav\AttributeIdInterface;
use MsgPhp\Eav\Entity\Attribute as BaseAttribute;

/**
 * @ORM\Entity()
 *
 * @final
 */
class Attribute extends BaseAttribute
{
    public const GOOGLE_OAUTH_ID = '9bbddea5-5c02-428b-a1ee-2ff51b271351';
    public const FACEBOOK_OAUTH_ID = '3d6f1eac-3863-4208-9593-262dd7e9b520';

    /** @ORM\Id @ORM\Column(type="msgphp_attribute_id", length=191) */
    private $id;

    public function __construct(AttributeIdInterface $id)
    {
        $this->id = $id;
    }

    public function getId(): AttributeIdInterface
    {
        return $this->id;
    }
}
