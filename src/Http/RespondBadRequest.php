<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\Response;

class RespondBadRequest extends Respond
{
    public function __construct()
    {
        $this->status = Response::HTTP_BAD_REQUEST;
    }
}
