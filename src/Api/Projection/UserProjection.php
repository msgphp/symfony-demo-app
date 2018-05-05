<?php

namespace App\Api\Projection;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use MsgPhp\Domain\Infra\Elasticsearch\DocumentMappingAwareInterface;
use MsgPhp\Domain\Projection\DomainProjectionInterface;

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
final class UserProjection implements DomainProjectionInterface, DocumentMappingAwareInterface
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
    public static function fromDocument(array $document): DomainProjectionInterface
    {
        $projection = new self();
        $projection->id = $document['id'] ?? null;
        $projection->email = $document['email'] ?? null;
        $projection->userId = $document['user_id'] ?? null;

        return $projection;
    }

    public static function getDocumentMapping(): array
    {
        return [
            'id' => 'text',
            'email' => 'text',
            'user_id' => 'text',
        ];
    }
}
