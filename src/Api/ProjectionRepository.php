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
        $params = [
            'index' => $this->index,
            'type' => $type,
            'body' => [
                'from' => $offset,
                'query' => ['match_all' => new \stdClass()],
            ],
        ];

        if ($limit) {
            $params['body']['size'] = $limit;
        }

        $documents = $this->client->search($params);

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

            return $document['_type']::fromDocument($document['_source']);
        } catch (Missing404Exception $e) {
            return null;
        }
    }

    public function clear(string $type): void
    {
        $this->client->deleteByQuery([
            'index' => $this->index,
            'type' => $type,
            'body' => [
                'query' => ['match_all' => new \stdClass()],
            ],
        ]);
    }

    public function save(ProjectionDocument $document): void
    {
        $params = ['index' => $this->index, 'type' => $document->getType(), 'body' => $document->getBody()];
        if (null !== $id = $document->getId()) {
            $params['id'] = $id;
        }

        $this->client->index($params);
    }

    public function delete(ProjectionDocument $document): void
    {
        $this->client->delete([
            'index' => $this->index,
            'type' => $document->getType(),
            'id' => $document->getId(),
        ]);
    }
}
