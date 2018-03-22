<?php

namespace App\Api;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;

final class ProjectionDataProvider implements CollectionDataProviderInterface, ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $typeRegistry;
    private $repository;

    public function __construct(ProjectionTypeRegistry $typeRegistry, ProjectionRepository $repository)
    {
        $this->typeRegistry = $typeRegistry;
        $this->repository = $repository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return in_array($resourceClass, $this->typeRegistry->all(), true);
    }

    /**
     * @return ProjectionInterface[]
     */
    public function getCollection(string $resourceClass, string $operationName = null): iterable
    {
        return $this->repository->findAll($resourceClass);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?ProjectionInterface
    {
        return $this->repository->find($resourceClass, $id);
    }
}
