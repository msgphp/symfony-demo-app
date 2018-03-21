<?php

namespace App\Api;

final class ProjectionSynchronization
{
    private $repository;
    private $providers;
    private $transformers;

    public function __construct(ProjectionRepository $repository, iterable $providers, array $transformers)
    {
        $this->repository = $repository;
        $this->providers = $providers;
        $this->transformers = $transformers;
    }

    /**
     * @return ProjectionDocument[]
     */
    public function synchronize(): iterable
    {
        // @todo wipe data
        foreach ($this->providers as $provider) {
            foreach ($provider() as $object) {
                $document = new ProjectionDocument();
                $document->source = $object;

                try {
                    $document->data = ($this->getTransformer($class = get_class($object)))($object);
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

    private function getTransformer(string $class): callable
    {
        if (isset($this->transformers[$class])) {
            return $this->transformers[$class];
        }

        foreach ($this->transformers as $transformerClass => $transformer) {
            if (is_subclass_of($class, $transformerClass)) {
                return $this->transformers[$class] = $transformer;
            }
        }

        throw new \LogicException(sprintf('No document transformer found for class "%s".', $class));
    }
}
