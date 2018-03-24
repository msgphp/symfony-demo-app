<?php

namespace App\Api\Projection\DocumentTransformer;

use App\Api\Projection\UserProjection;
use App\Entity\User\User;
use MsgPhp\Domain\Projection\DomainProjectionDocument;
use PascalDeVink\ShortUuid\ShortUuid;

final class UserDocumentTransformer
{
    // @todo inject as parameter, should be changed per app and is secret
    private const DOCUMENT_UUID_NS = 'ee5b8c83-f12d-41f5-bcf9-3e83b7558317';

    public function __invoke(User $user): DomainProjectionDocument
    {
        // @todo leverage service for DomainIdInterface types
        $userId = $user->getId()->toString();
        $docId = ShortUuid::uuid5(self::DOCUMENT_UUID_NS, sha1($userId));

        return DomainProjectionDocument::create(UserProjection::class, $docId, [
            'id' => $docId,
            'email' => $user->getEmail(),
            'user_id' => $userId,
        ]);
    }
}
