<?php

namespace App\Api\Projection\Document\Transformer;

use App\Api\Projection\Document\DocumentIdentity;
use App\Api\Projection\UserProjection;
use App\Entity\User\User;
use MsgPhp\Domain\Projection\ProjectionDocument;

final class UserDocumentTransformer
{
    private $documentIdentity;

    public function __construct(DocumentIdentity $documentIdentity)
    {
        $this->documentIdentity = $documentIdentity;
    }

    public function __invoke(User $user): ProjectionDocument
    {
        $docId = $this->documentIdentity->identifyId($userId = $user->getId());

        return new ProjectionDocument(UserProjection::class, $docId, [
            'id' => $docId,
            'email' => $user->getEmail(),
            'user_id' => $userId->toString(),
        ]);
    }
}
