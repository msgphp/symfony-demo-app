<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use MsgPhp\User\Entity\Fields\UserField;

/**
 * @ORM\Entity()
 * @ORM\AssociationOverrides({
 *     @ORM\AssociationOverride(name="user", fetch="EAGER")
 * })
 *
 * @final
 */
class OneTimeLoginToken
{
    use UserField;

    /** @ORM\Column(unique=true) */
    private $token;

    public function __construct(User $user, string $token = null)
    {
        $this->user = $user;
        $this->token = $token ?? bin2hex(random_bytes(32));
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
