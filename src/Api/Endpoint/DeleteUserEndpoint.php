<?php

declare(strict_types=1);

namespace App\Api\Endpoint;

use App\Api\Projection\UserProjection;
use MsgPhp\User\Command\DeleteUser;
use MsgPhp\User\Infrastructure\Uuid\UserUuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

final class DeleteUserEndpoint
{
    public function __invoke(Request $request, UserProjection $data, MessageBusInterface $bus)
    {
        $bus->dispatch(new DeleteUser(UserUuid::fromValue($data->userId)));

        return null;
    }
}
