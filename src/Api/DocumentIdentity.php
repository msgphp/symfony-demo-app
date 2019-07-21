<?php

declare(strict_types=1);

namespace App\Api;

use MsgPhp\Domain\DomainId;
use PascalDeVink\ShortUuid\ShortUuid;

abstract class DocumentIdentity
{
    private const ID_NS = 'ee5b8c83-f12d-41f5-bcf9-3e83b7558317';

    /**
     * @param string|DomainId $value
     */
    public static function get($value): string
    {
        if ($value instanceof DomainId) {
            $value = $value->toString();
        }
        if ('' === $value) {
            throw new \LogicException('A document identifier cannot be obtained from an empty identifier.');
        }

        return ShortUuid::uuid5(self::ID_NS, sha1($value));
    }
}
