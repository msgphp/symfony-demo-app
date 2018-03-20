<?php

namespace App\Api\Resource;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;

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
final class User
{
    /**
     * @ApiProperty(identifier=true)
     */
    public $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
