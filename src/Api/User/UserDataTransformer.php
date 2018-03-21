<?php

namespace App\Api\User;

use App\Api\ProjectionInterface;
use App\Api\Resource\User as UserProjection;
use App\Entity\User\User;

final class UserDataTransformer
{
    public function transform(User $user): ProjectionInterface
    {
        return UserProjection::fromDocument(['id' => $user->getId()->toString()]);
    }
}
