<?php

namespace App\Api;

interface ProjectionInterface
{
    public static function fromDocument(array $data): self;

    public function toDocument(): array;
}
