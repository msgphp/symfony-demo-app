<?php

namespace App\Api\Projection;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource(shortName="EsUser")
 */
class EsUserProjection
{
    /**
     * @var string Globally unique resource identifier
     * @ApiProperty(identifier=true)
     */
    public $id;

    /**
     * @var string Primary e-mail address
     */
    public $email;

    /**
     * @var string|null Globally unique domain identifier (Optional in "write")
     */
    public $userId;
}
