<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use MsgPhp\User\Entity\UserSecondaryEmail as BaseUserSecondaryEmail;

/**
 * @ORM\Entity()
 * @ORM\AssociationOverrides({
 *     @ORM\AssociationOverride(name="user", inversedBy="secondaryEmails")
 * })
 *
 * @final
 */
class UserSecondaryEmail extends BaseUserSecondaryEmail
{
}
