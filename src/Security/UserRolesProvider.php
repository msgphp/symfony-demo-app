<?php

namespace App\Security;

use App\Entity\User\User;
use App\Entity\User\UserRole;
use MsgPhp\User\Entity\User as BaseUser;
use MsgPhp\User\Infra\Security\UserRolesProviderInterface;

final class UserRolesProvider implements UserRolesProviderInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_DISABLED_USER = 'ROLE_DISABLED_USER';
    public const ROLE_USER = 'ROLE_USER';

    /**
     * @param User $user
     */
    public function getRoles(BaseUser $user): array
    {
        $roles = $user->isEnabled() ? [self::ROLE_USER] : [self::ROLE_DISABLED_USER];

        return array_merge($roles, $user->getRoles()->map(function (UserRole $userRole) {
            return $userRole->getRoleName();
        }));
    }
}
