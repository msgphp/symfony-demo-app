<?php

namespace App\Api\Endpoint;

use App\Api\Projection\UserProjection;
use MsgPhp\Domain\Projection\ProjectionInterface;
use MsgPhp\Domain\Projection\ProjectionRepositoryInterface;
use MsgPhp\User\Command\DeleteUserCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

final class DispatchMessageEndpoint
{
    public function __invoke(Request $request, ProjectionInterface $data, ProjectionRepositoryInterface $projectionRepository, MessageBusInterface $bus)
    {
        if ($data instanceof UserProjection && $request->isMethod(Request::METHOD_DELETE)) {
            $bus->dispatch(new DeleteUserCommand($data->userId));
            $projectionRepository->delete(get_class($data), $data->id);
        }

        return null;
    }
}
