<?php

namespace App\Api\Endpoint;

use App\Entity\User\User;
use MsgPhp\Domain\Projection\DomainProjectionDocumentTransformerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

final class MeEndpoint
{
    /**
     * @ParamConverter("user", converter="msgphp.current_user")
     */
    public function __invoke(User $user, DomainProjectionDocumentTransformerInterface $transformer)
    {
        return $transformer->transform($user)->toProjection();
    }
}
