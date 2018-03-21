<?php

namespace App\Api;

final class ProjectionDocument
{
    private const DATA_TYPE_KEY = 'document_type';
    private const DATA_ID_KEY = 'document_id';

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

    public function getType(): string
    {
        if (!isset($this->data[self::DATA_TYPE_KEY])) {
            throw new \LogicException('Document type not set.');
        }

        if (!is_subclass_of($type = $this->data[self::DATA_TYPE_KEY], ProjectionInterface::class)) {
            throw new \LogicException(sprintf('Document type must be a sub class of "%s", got "%s".', ProjectionInterface::class, $type));
        }

        return $type;
    }

    public function getId(): ?string
    {
        return $this->data[self::DATA_ID_KEY] ?? null;
    }

    public function getBody(): array
    {
        $data = $this->data;
        unset($data[self::DATA_TYPE_KEY], $data[self::DATA_ID_KEY]);

        return $data;
    }
}
