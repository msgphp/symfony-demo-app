<?php

namespace App\Api\Projection;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use MsgPhp\Domain\Infra\Elasticsearch\DocumentMappingProviderInterface;
use MsgPhp\Domain\Projection\ProjectionInterface;

/**
 * @ApiResource(
 *     shortName="User",
 *     collectionOperations={
 *         "get",
 *     },
 *     itemOperations={
 *         "get",
 *         "delete"={
 *             "controller"="App\Api\Endpoint\DispatchMessageEndpoint",
 *         },
 *     }
 * )
 */
class UserProjection implements ProjectionInterface, DocumentMappingProviderInterface
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
        $projection = new static();
        $projection->id = $document['id'] ?? null;
        $projection->email = $document['email'] ?? null;
        $projection->userId = $document['user_id'] ?? null;

        return $projection;
    }

    public static function provideDocumentMappings(): iterable
    {
        yield static::class => [
            'id' => 'text',
            'email' => 'text',
            'user_id' => 'text',
        ];
    }
}
