<?php

namespace App\Api\User;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Api\Resource\User;

final class UserDataProvider implements CollectionDataProviderInterface, ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return User::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null): iterable
    {
        yield User::fromDocument(['id' => 'foo']);
        yield User::fromDocument(['id' => 'bar']);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?User
    {
        return User::fromDocument(['id' => $id]);
    }
}
