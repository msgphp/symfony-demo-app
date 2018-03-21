<?php

namespace App\Api;

use Elasticsearch\Client;

final class ProjectionTypeRegistry
{
    private $client;
    private $indexes;
    private $mappings;
    private $settings;

    public function __construct(Client $client, array $indexes, array $mappings, array $settings = [])
    {
        foreach ($mappings as $type =>  $mapping) {
            if (!isset($mapping['properties']) || !is_array($mapping['properties'])) {
                continue;
            }

            foreach ($mapping['properties'] as $property => $info) {
                if (!is_array($info)) {
                    $info = ['type' => $info ?? 'string'];
                } elseif (!isset($info['type'])) {
                    $info['type'] = 'string';
                }

                $mappings[$type]['properties'][$property] = $info;
            }
        }

        $this->client = $client;
        $this->indexes = $indexes;
        $this->mappings = $mappings;
        $this->settings = $settings;
    }

    public function initialize(): void
    {
        foreach ($this->indexes as $index) {
            $this->client->indices()->create([
                'index' => $index,
                'body' => [
                    'settings' => $this->settings,
                    'mappings' => $this->mappings,
                ],
            ]);
        }
    }

    public function reset(): void
    {
        foreach ($this->indexes as $index) {
            $this->client->indices()->delete(['index' => $index]);
        }
    }
}
