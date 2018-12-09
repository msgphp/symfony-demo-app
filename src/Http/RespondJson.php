<?php

namespace App\Http;

final class RespondJson extends Respond
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
