<?php

namespace App\Http;

final class RespondStream extends Respond
{
    public $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }
}
