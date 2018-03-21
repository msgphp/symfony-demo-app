<?php

namespace App\Api;

final class ProjectionDocument
{
    public const STATUS_UNKNOWN = 1;
    public const STATUS_VALID = 2;
    public const STATUS_SKIPPED = 3;

    /** @var int */
    public $status = self::STATUS_UNKNOWN;

    /** @var array */
    public $data = [];

    /** @var object|null */
    public $source;

    /** @var \Exception|null $error */
    public $error;
}
