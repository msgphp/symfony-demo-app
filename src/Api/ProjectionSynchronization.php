<?php

namespace App\Api;

final class ProjectionSynchronization
{
    private $repository;

    public function __construct(ProjectionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function synchronize(string $type)
    {

    }
}
