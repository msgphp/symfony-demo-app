<?php

declare(strict_types=1);

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
 *             "controller"="App\Api\Endpoint\CreateUserEndpoint",
 *         },
 *     },
 *     itemOperations={
 *         "get",
 *         "delete"={
 *             "controller"="App\Api\Endpoint\DeleteUserEndpoint",
 *         },
 *     },
 *     normalizationContext={"groups"={"user:read"}},
 *     denormalizationContext={"groups"={"user:write"}},
 * )
 */
class UserProjection implements ProjectionInterface, DocumentMappingProviderInterface
{
    /**
     * @var string Globally unique resource identifier
     * @ApiProperty(identifier=true)
     * @Groups({"user:read"})
     */
    public $id;

    /**
     * @var null|string Globally unique domain identifier (Optional in "write")
     * @Groups({"user:read", "user:write"})
     */
    public $userId;

    /**
     * @var string Primary e-mail address
     * @Groups({"user:read", "user:write"})
     */
    public $email;

    /**
     * @var null|string Plain password (Required in "write")
     * @Groups({"user:write"})
     */
    public $password;

    /**
     * @return static
     */
    public static function fromDocument(array $document): ProjectionInterface
    {
        $projection = new static();
        $projection->id = $document['id'] ?? null;
        $projection->userId = $document['user_id'] ?? null;
        $projection->email = $document['email'] ?? null;

        return $projection;
    }

    public static function provideDocumentMappings(): iterable
    {
        yield static::class => [
            'id' => 'text',
            'user_id' => 'text',
            'email' => 'text',
        ];
    }
}
