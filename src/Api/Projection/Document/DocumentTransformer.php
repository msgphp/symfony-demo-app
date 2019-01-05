<?php

declare(strict_types=1);

namespace App\Api\Projection\Document;

use App\Entity\User\User;
use MsgPhp\Domain\Projection\ProjectionDocument;
use MsgPhp\Domain\Projection\ProjectionDocumentTransformerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

final class DocumentTransformer implements ProjectionDocumentTransformerInterface, ServiceSubscriberInterface
{
    private const MAPPING = [
        User::class => Transformer\UserDocumentTransformer::class,
    ];

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function transform($object): ProjectionDocument
    {
        foreach (self::MAPPING as $type => $service) {
            if ($object instanceof $type) {
                return ($this->container->get($service))($object);
            }
        }

        throw new \LogicException(sprintf('No supporting transformer found for class "%s".', \get_class($object)));
    }

    public static function getSubscribedServices(): array
    {
        return array_values(self::MAPPING);
    }
}
