<?php

namespace App\Http;

final class RespondFile extends Respond
{
    public $file;

    public function __construct($file)
    {
        $this->file = $file;
    }
}
