<?php

namespace App\Api\Resource;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\ProjectionInterface;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get"
 *     },
 *     itemOperations={
 *         "get"
 *     }
 * )
 */
final class User implements ProjectionInterface
{
    /**
     * @var string Globally unique identifier
     * @ApiProperty(identifier=true)
     */
    public $id;

    /**
     * @var string Primary e-mail address
     */
    public $email;

    /**
     * @return $this
     */
    public static function fromDocument(array $document): ProjectionInterface
    {
        $projection = new self();
        $projection->id = $document['id'] ?? null;
        $projection->email = $document['email'] ?? null;

        return $projection;
    }

    public function toDocument(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
        ];
    }
}
