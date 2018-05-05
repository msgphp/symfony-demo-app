<?php

namespace App\Api\Projection\DocumentTransformer;

use App\Entity\User\User;
use MsgPhp\Domain\Projection\DomainProjectionDocument;
use MsgPhp\Domain\Projection\DomainProjectionDocumentTransformerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ServiceSubscriberInterface;

final class DocumentTransformer implements DomainProjectionDocumentTransformerInterface, ServiceSubscriberInterface
{
    private const MAPPING = [
        User::class => UserDocumentTransformer::class,
    ];

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function transform($object): DomainProjectionDocument
    {
        foreach (self::MAPPING as $type => $service) {
            if ($object instanceof $type) {
                return ($this->container->get($service))($object);
            }
        }

        throw new \LogicException(sprintf('No supporting transformer found for class "%s".', get_class($object)));
    }

    public static function getSubscribedServices(): array
    {
        return array_values(self::MAPPING);
    }
}
