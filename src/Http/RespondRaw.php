<?php

namespace App\Http;

class RespondRaw extends Respond
{
    public $contents;

    public function __construct(string $contents)
    {
        $this->contents = $contents;
    }
}
