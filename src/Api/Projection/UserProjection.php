<?php

declare(strict_types=1);

namespace App\Api\Projection;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\Filter\MatchFilter;
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
 * @ApiFilter(MatchFilter::class, properties={"email"})
 */
class UserProjection
{
    /**
     * @var string Globally unique resource identifier
     * @ApiProperty(identifier=true)
     * @Groups({"user:read"})
     */
    public $id;

    /**
     * @var string|null Globally unique domain identifier (Optional in "write")
     * @Groups({"user:read", "user:write"})
     */
    public $userId;

    /**
     * @var string Primary e-mail address
     * @Groups({"user:read", "user:write"})
     */
    public $email;

    /**
     * @var string|null Plain password (Required in "write")
     * @Groups({"user:write"})
     */
    public $password;
}
