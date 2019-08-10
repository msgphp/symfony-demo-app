<?php

declare(strict_types=1);

namespace App\Api\Serializer;

use App\Api\DocumentIdentity;
use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class UserNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param User        $object
     * @param string|null $format
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $userId = $object->getId();

        return [
            'id' => DocumentIdentity::get($userId),
            'user_id' => $userId->toString(),
            'email' => $object->getEmail(),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof User;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
