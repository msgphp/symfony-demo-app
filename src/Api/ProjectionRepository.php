<?php

namespace App\Api;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;

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
        $documents = $this->client->search([
            'index' => $this->index,
            'type' => $type,
            'body' => [
                'query' => ['match_all' => new \stdClass()],
            ],
        ]);

        foreach ($documents['hits']['hits'] ?? [] as $document) {
            yield $document['_type']::fromDocument($document['_source']);
        }
    }

    public function find(string $type, string $id): ?ProjectionInterface
    {
        try {
            $document = $this->client->get([
                'index' => $this->index,
                'type' => $type,
                'id' => $id,
            ]);
        } catch (Missing404Exception $e) {
            return null;
        }

        return $document['_type']::fromDocument($document['_source']);
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
