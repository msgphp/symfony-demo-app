<?php

namespace App\Api\Projection;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use MsgPhp\Domain\Infra\Elasticsearch\DocumentMappingProviderInterface;
use MsgPhp\Domain\Projection\ProjectionInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     shortName="User",
 *     collectionOperations={
 *         "get",
 *         "post"={
 *             "controller"="App\Api\Endpoint\DispatchMessageEndpoint",
 *         },
 *     },
 *     itemOperations={
 *         "get",
 *         "delete"={
 *             "controller"="App\Api\Endpoint\DispatchMessageEndpoint",
 *         },
 *     },
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}},
 * )
 */
class UserProjection implements ProjectionInterface, DocumentMappingProviderInterface
{
    /**
     * @var string Globally unique resource identifier
     * @ApiProperty(identifier=true)
     * @Groups({"read"})
     */
    public $id;

    /**
     * @var string Primary e-mail address
     * @Groups({"read", "write"})
     */
    public $email;

    /**
     * @var string|null Globally unique domain identifier (Optional in "write")
     * @Groups({"read", "write"})
     */
    public $userId;

    /**
     * @var string|null Plain password (Required in "write")
     * @Groups({"write"})
     */
    public $password;

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
