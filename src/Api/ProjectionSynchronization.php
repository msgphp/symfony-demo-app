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

    public function synchronize()
    {
        foreach ($this->providers as $provider) {
            foreach ($provider() as $object) {
                $document = ($this->getTransformer($object))($object);
            }
        }
    }

    /**
     * @param object $object
     */
    private function getTransformer($object): callable
    {
        if (isset($this->transformers[$class = get_class($object)])) {
            return $this->transformers[$class];
        }

        foreach ($this->transformers as $transformerClass => $transformer) {
            if (is_subclass_of($class, $transformerClass)) {
                return $this->transformers[$class] = $transformer;
            }
        }

        throw new \LogicException(sprintf('No document transformer found for object "%s".', $class));
    }
}
