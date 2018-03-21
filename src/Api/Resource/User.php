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
     * @return $this
     */
    public static function fromDocument(array $document): ProjectionInterface
    {
        $projection = new self();
        $projection->id = $document['id'] ?? null;

        return $projection;
    }

    public function toDocument(): array
    {
        return [
            'type' => get_class($this),
            'id' => $this->id,
        ];
    }
}
