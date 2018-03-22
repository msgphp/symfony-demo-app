<?php

namespace App\Api;

use App\Api\ProjectionDocument;
use App\Api\Projection\UserProjection as UserResource;
use App\Entity\User\User;
use PascalDeVink\ShortUuid\ShortUuid;
use Psr\Container\ContainerInterface;

final class ProjectionDocumentTransformer
{
    private $transformers;

    public function __construct(ContainerInterface $transformers)
    {
        $this->transformers = $transformers;
    }

    /**
     * @param object $object
     */
    public function transform($object): ProjectionDocument
    {
        if (!$this->transformers->has($class = get_class($object))) {
            throw new \LogicException(sprintf('No projection document transformer available for class "%s".', $class));
        }

        if (!is_callable($transformer = $this->transformers->get($class))) {
            throw new \LogicException(sprintf('Projection document transformer for "%s" must be a callable, got "%s".', $class, gettype($transformer)));
        }

        $document = $transformer($object);

        if (!$document instanceof ProjectionDocument) {
            throw new \LogicException(sprintf('Projection document transformer must for "%s" must return an instance of "%s", got "%s".', $class, ProjectionDocument::class, is_object($document) ? get_class($document) : gettype($document)));
        }

        $document->source = $object;

        return $document;
    }
}
