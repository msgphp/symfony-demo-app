<?php

namespace App\Api;

use Elasticsearch\Client;

final class ProjectionRepository
{
    private $client;
    private $index;

    public function __construct(Client $client, string $index)
    {
        $this->client = $client;
        $this->index = $index;
    }

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

    public function save(ProjectionDocument $document): void
    {
        $params = ['index' => $this->index, 'type' => $document->getType(), 'body' => $document->getBody()];
        if (null !== $id = $document->getId()) {
            $params['id'] = $id;
        }

        $this->client->index($params);
    }

    /**
     * @param ProjectionDocument[] $documents
     */
    public function saveAll(iterable $projections): void
    {

    }

    public function delete(ProjectionDocument $document): void
    {

    }

    /**
     * @param ProjectionDocument[] $documents
     */
    public function deleteAll(iterable $projections): void
    {

    }
}
