<?php

namespace App\Api\User;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Api\ProjectionRepository;
use App\Api\Resource\User;

final class UserDataProvider implements CollectionDataProviderInterface, ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $repository;

    public function __construct(ProjectionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return User::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null): iterable
    {
        return $this->repository->findAll($resourceClass);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?User
    {
        return $this->repository->find($resourceClass, $id);
    }
}
