<?php

namespace App\Api\Projection;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\ProjectionInterface;

/**
 * @ApiResource(
 *     shortName="User",
 *     collectionOperations={
 *         "get"
 *     },
 *     itemOperations={
 *         "get"
 *     }
 * )
 */
final class UserProjection implements ProjectionInterface
{
    /**
     * @var string Globally unique resource identifier
     * @ApiProperty(identifier=true)
     */
    public $id;

    /**
     * @var string Primary e-mail address
     */
    public $email;

    /**
     * @var string Globally unique domain identifier
     */
    public $userId;

    /**
     * @return $this
     */
    public static function fromDocument(array $document): ProjectionInterface
    {
        $projection = new self();
        $projection->id = $document['id'] ?? null;
        $projection->email = $document['email'] ?? null;
        $projection->userId = $document['user_id'] ?? null;

        return $projection;
    }
}