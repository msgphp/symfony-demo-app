<?php

namespace App\Api;

final class ProjectionRepository
{
    /**
     * @return ProjectionInterface[]
     */
    public function findAll(string $type, int $offset = 0, int $limit = 0): iterable
    {

    }

    public function find(string $type, $id): ProjectionInterface
    {

    }

    public function clear(string $type): void
    {

    }

    public function save(ProjectionInterface $projection): void
    {

    }

    /**
     * @param ProjectionInterface[] $projections
     */
    public function saveAll(iterable $projections): void
    {

    }

    public function delete(ProjectionInterface $projection): void
    {

    }

    /**
     * @param ProjectionInterface[] $projections
     */
    public function deleteAll(iterable $projections): void
    {

    }
}
