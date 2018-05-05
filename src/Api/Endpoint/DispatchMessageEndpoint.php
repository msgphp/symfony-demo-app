<?php

namespace App\Api\Endpoint;

use App\Api\Projection\UserProjection;
use MsgPhp\Domain\Message\MessageDispatchingTrait;
use MsgPhp\User\Command\DeleteUserCommand;
use Symfony\Component\HttpFoundation\Request;

final class DispatchMessageEndpoint
{
    use MessageDispatchingTrait;

    public function __invoke(Request $request, $data)
    {
        if ($data instanceof UserProjection && $request->isMethod(Request::METHOD_DELETE)) {
            $this->dispatch(DeleteUserCommand::class, [$data->userId]);
        }

        return null;
    }
}
