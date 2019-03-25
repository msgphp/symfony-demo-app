<?php

declare(strict_types=1);

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use MsgPhp\User\Username as BaseUsername;

/**
 * @ORM\Entity()
 *
 * @final
 */
class Username extends BaseUsername
{
}
