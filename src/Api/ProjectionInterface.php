<?php

namespace App\Api;

interface ProjectionInterface
{
    public static function fromDocument(array $document): self;
}
