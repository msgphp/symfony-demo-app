<?php

namespace App\Api\User;

use App\Api\Resource\User as UserResource;
use App\Entity\User\User;
use PascalDeVink\ShortUuid\ShortUuid;

final class UserDocumentTransformer
{
    private const DOC_NS = 'ee5b8c83-f12d-41f5-bcf9-3e83b7558317';

    public function __invoke(User $user): array
    {
        // @todo leverage service for DomainIdInterface types
        $userId = $user->getId()->toString();
        $docId = ShortUuid::uuid5(self::DOC_NS, sha1($userId));

        return [
            'document_type' => UserResource::class,
            'document_id' => $docId,
            'id' => $docId,
            'email' => $user->getEmail(),
            'user_id' => $userId,
        ];
    }
}
