<?php

namespace App\Api\User;

use App\Entity\User\User;

final class UserDocumentTransformer
{
    public function __invoke(User $user): array
    {
        return [
            'id' => $user->getId()->toString(),
            'email' => $user->getEmail(),
        ];
    }
}
