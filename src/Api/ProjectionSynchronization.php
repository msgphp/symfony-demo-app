<?php

namespace App\Api;

final class ProjectionSynchronization
{
    private $typeRegistry;
    private $repository;
    private $providers;
    private $transformer;

    public function __construct(ProjectionTypeRegistry $typeRegistry, ProjectionRepository $repository, ProjectionDocumentTransformer $transformer, iterable $providers)
    {
        $this->typeRegistry = $typeRegistry;
        $this->repository = $repository;
        $this->providers = $providers;
        $this->transformer = $transformer;
    }

    /**
     * @return ProjectionDocument[]
     */
    public function synchronize(): iterable
    {
        foreach ($this->typeRegistry->all() as $type) {
            $this->repository->clear($type);
        }

        foreach ($this->providers as $provider) {
            foreach ($provider() as $object) {
                try {
                    $document = $this->transformer->transform($object);
                } catch (\Exception $e) {
                    $document = new ProjectionDocument();
                    $document->status = ProjectionDocument::STATUS_SKIPPED;
                    $document->source = $object;
                    $document->error = $e;

                    yield $document;
                    continue;
                }

                try {
                    $document->status = ProjectionDocument::STATUS_VALID;

                    $this->repository->save($document);
                } catch (\Exception $e) {
                    $document->status = ProjectionDocument::STATUS_SKIPPED;
                    $document->error = $e;
                } finally {
                    yield $document;
                }
            }
        }
    }
}
