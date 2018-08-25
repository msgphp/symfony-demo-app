<?php

namespace App\Api\Endpoint;

use App\Api\Projection\Document\DocumentIdentity;
use App\Api\Projection\UserProjection;
use MsgPhp\Domain\Projection\ProjectionInterface;
use MsgPhp\User\Command\{CreateUserCommand, DeleteUserCommand};
use MsgPhp\User\Infra\Uuid\UserId;
use MsgPhp\User\Password\PasswordHashingInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class DispatchMessageEndpoint
{
    public function __invoke(Request $request, ProjectionInterface $data, MessageBusInterface $bus, UrlGeneratorInterface $urlGenerator, PasswordHashingInterface $passwordHashing, DocumentIdentity $documentIdentity)
    {
        if ($data instanceof UserProjection) {
            if ($request->isMethod(Request::METHOD_DELETE)) {
                $bus->dispatch(new DeleteUserCommand($data->userId));

                return null;
            }

            if ($request->isMethod(Request::METHOD_POST)) {
                $docId = $documentIdentity->identifyId($userId = UserId::fromValue($data->userId));
                $locationUrl = $urlGenerator->generate('api_users_get_item', ['id' => $docId], UrlGeneratorInterface::ABSOLUTE_URL);

                return new JsonResponse(['id' => $docId, 'user_id' => $userId->toString()], JsonResponse::HTTP_CREATED, ['Location' => $locationUrl]);
            }
        }

        throw new \LogicException('Unexpected API operation');
    }
}
