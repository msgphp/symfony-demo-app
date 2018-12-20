<?php

declare(strict_types=1);

namespace App\Api\Endpoint;

use App\Api\Projection\Document\DocumentIdentity;
use App\Api\Projection\UserProjection;
use MsgPhp\User\Command\CreateUserCommand;
use MsgPhp\User\Infra\Uuid\UserId;
use MsgPhp\User\Password\PasswordHashingInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CreateUserEndpoint
{
    public function __invoke(Request $request, UserProjection $data, MessageBusInterface $bus, UrlGeneratorInterface $urlGenerator, PasswordHashingInterface $passwordHashing, DocumentIdentity $documentIdentity)
    {
        $userId = UserId::fromValue($data->userId);
        $docId = $documentIdentity->identifyId($userId);
        $locationUrl = $urlGenerator->generate('api_users_get_item', ['id' => $docId], UrlGeneratorInterface::ABSOLUTE_URL);

        $bus->dispatch(new CreateUserCommand([
            'id' => $userId,
            'email' => $data->email,
            'password' => $passwordHashing->hash($data->password),
        ]));

        // @todo refresh $data + return?
        //       then add location header via event listener

        return new JsonResponse(['id' => $docId], JsonResponse::HTTP_CREATED, ['Location' => $locationUrl]);
    }
}
