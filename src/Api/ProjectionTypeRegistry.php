<?php

namespace App\Api;

use Elasticsearch\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ProjectionTypeRegistry
{
    private $client;
    private $index;
    private $mappings;
    private $settings;
    private $logger;
    private $types;

    public function __construct(Client $client, string $index, array $mappings, array $settings = [], LoggerInterface $logger = null)
    {
        foreach ($mappings as $type =>  $mapping) {
            if (!isset($mapping['properties']) || !is_array($mapping['properties'])) {
                continue;
            }

            foreach ($mapping['properties'] as $property => $info) {
                if (!is_array($info)) {
                    $info = ['type' => $info ?? 'text'];
                }

                $mappings[$type]['properties'][$property] = $info + ['type' => 'text'];
            }
        }

        $this->client = $client;
        $this->index = $index;
        $this->mappings = $mappings;
        $this->settings = $settings;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @return string[]
     */
    public function all(): array
    {
        if (null === $this->types) {
            $this->types = [];
            foreach (array_keys($this->mappings) as $type) {
                if (is_subclass_of($type, ProjectionInterface::class)) {
                    $this->types[] = $type;
                }
            }
        }

        return $this->types;
    }

    public function initialize(): void
    {
        $indices = $this->client->indices();

        if ($indices->exists($params = ['index' => $this->index])) {
            return;
        }

        if ($this->settings) {
            $params['body']['settings'] = $this->settings;
        }

        if ($this->mappings) {
            $params['body']['mappings'] = $this->mappings;
        }

        $indices->create($params);
        $this->logger->info('Initialized Elasticsearch index "{index}".', ['index' => $this->index]);
    }

    public function destroy(): void
    {
        $indices = $this->client->indices();

        if ($indices->exists($params = ['index' => $this->index])) {
            $indices->delete($params);
            $this->logger->info('Destroyed Elasticsearch index "{index}".', ['index' => $this->index]);
        }
    }
}
