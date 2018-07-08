<?php

namespace App\Api\Projection\Document;

use MsgPhp\Domain\DomainIdInterface;
use PascalDeVink\ShortUuid\ShortUuid;

final class DocumentIdentity
{
    private const UUID_NS = 'ee5b8c83-f12d-41f5-bcf9-3e83b7558317';

    public function identify(string $value): string
    {
        return ShortUuid::uuid5(self::UUID_NS, sha1($value));
    }

    public function identifyId(DomainIdInterface $id): string
    {
        if ($id->isEmpty()) {
            throw new \LogicException('A document identifier cannot be obtained from an empty identifier.');
        }

        return $this->identify($id->toString());
    }
}
