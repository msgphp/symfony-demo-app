<?php

declare(strict_types=1);

namespace App\Api\Endpoint;

use App\Api\DocumentIdentity;
use App\Api\Projection\UserProjection;
use MsgPhp\User\Command\CreateUser;
use MsgPhp\User\Infrastructure\Uuid\UserUuid;
use MsgPhp\User\Password\PasswordHashing;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CreateUserEndpoint
{
    public function __invoke(Request $request, UserProjection $data, MessageBusInterface $bus, UrlGeneratorInterface $urlGenerator, PasswordHashing $passwordHashing)
    {
        $userId = UserUuid::fromValue($data->userId);
        $docId = DocumentIdentity::get($userId);
        $locationUrl = $urlGenerator->generate('api_users_get_item', ['id' => $docId], UrlGeneratorInterface::ABSOLUTE_URL);

        if (null === $data->password) {
            throw new BadRequestHttpException('Missing password field.');
        }

        $bus->dispatch(new CreateUser([
            'id' => $userId,
            'email' => $data->email,
            'password' => $passwordHashing->hash($data->password),
        ]));

        return new JsonResponse(['id' => $docId], JsonResponse::HTTP_CREATED, ['Location' => $locationUrl]);
    }
}
