<?php

namespace App\Api\Endpoint;

use App\Api\Projection\UserProjection;
use MsgPhp\Domain\Projection\ProjectionInterface;
use MsgPhp\User\Command\DeleteUserCommand;
use MsgPhp\User\Password\PasswordHashingInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

final class DispatchMessageEndpoint
{
    public function __invoke(Request $request, ProjectionInterface $data, MessageBusInterface $bus, PasswordHashingInterface $passwordHashing)
    {
        if ($data instanceof UserProjection) {
            if ($request->isMethod(Request::METHOD_DELETE)) {
                $bus->dispatch(new DeleteUserCommand($data->userId));

                return null;
            }

            if ($request->isMethod(Request::METHOD_POST)) {
                $data->id = 'foo';

                return $data;
            }
        }

        throw new \LogicException('Unexpected API operation');
    }
}
