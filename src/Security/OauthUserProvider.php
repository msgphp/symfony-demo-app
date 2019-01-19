<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Eav\Attribute;
use App\Entity\User\UserAttributeValue;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use MsgPhp\Domain\Exception\EntityNotFoundException;
use MsgPhp\Eav\Infra\Uuid\AttributeId;
use MsgPhp\User\Command\{AddUserAttributeValueCommand, ConfirmUserCommand, CreateUserCommand};
use MsgPhp\User\Infra\Security\SecurityUserProvider;
use MsgPhp\User\Infra\Uuid\UserId;
use MsgPhp\User\Repository\{UserAttributeValueRepositoryInterface, UserRepositoryInterface};
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

final class OauthUserProvider implements OAuthAwareUserProviderInterface
{
    private $userRepository;
    private $userAttributeValueRepository;
    private $securityUserProvider;
    private $bus;

    public function __construct(UserRepositoryInterface $userRepository, UserAttributeValueRepositoryInterface $userAttributeValueRepository, SecurityUserProvider $securityUserProvider, MessageBusInterface $bus)
    {
        $this->userRepository = $userRepository;
        $this->userAttributeValueRepository = $userAttributeValueRepository;
        $this->securityUserProvider = $securityUserProvider;
        $this->bus = $bus;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $owner = $response->getResourceOwner()->getName();
        $username = $response->getUsername();

        if (!\defined($const = Attribute::class.'::'.strtoupper($owner).'_OAUTH_ID')) {
            throw new \LogicException(sprintf('Missing constant "%s" for OAuth resoure owner "%s"', $const, $owner));
        }

        $attributeId = AttributeId::fromValue(\constant($const));
        $userAttributeValues = $this->userAttributeValueRepository->findAllByAttributeIdAndValue($attributeId, $username);

        if ($userAttributeValues->isEmpty()) {
            if (null === $email = $response->getEmail()) {
                throw new CustomUserMessageAuthenticationException(sprintf('Oauth resource owner "%s" requires e-mail availability and appropriate read-privilege.', $owner));
            }

            try {
                $user = $this->userRepository->findByUsername($email);
                $userId = $user->getId();
            } catch (EntityNotFoundException $e) {
                $userId = new UserId();
                /** @todo validate username/email availability */
                $this->bus->dispatch(new CreateUserCommand([
                    'id' => $userId,
                    'email' => $email,
                    'password' => bin2hex(random_bytes(32)),
                ]));
                $this->bus->dispatch(new ConfirmUserCommand($userId));

                $user = $this->userRepository->find($userId);
            }

            $this->bus->dispatch(new AddUserAttributeValueCommand($userId, $attributeId, $username));
        } else {
            /** @var UserAttributeValue $userAttributeValue */
            $userAttributeValue = $userAttributeValues->first();
            $user = $userAttributeValue->getUser();
        }

        return $this->securityUserProvider->fromUser($user);
    }
}
