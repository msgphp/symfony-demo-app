<?php

namespace App\Api\User;

use App\Api\Resource\User as UserResource;
use App\Entity\User\User;
use PascalDeVink\ShortUuid\ShortUuid;

final class UserDocumentTransformer
{
    public function __invoke(User $user): array
    {
        return [
            'document_type' => UserResource::class,
            'document_id' => $id = ShortUuid::uuid4(),
            'id' => $id,
            'email' => $user->getEmail(),
            'user_id' => $user->getId()->toString(),
        ];
    }
}
