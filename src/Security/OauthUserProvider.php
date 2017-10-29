<?php

namespace App\Security;

use App\Entity\Eav\Attribute;
use App\Entity\User\UserAttributeValue;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use MsgPhp\Domain\CommandBusInterface;
use MsgPhp\Domain\Entity\EntityFactoryInterface;
use MsgPhp\Domain\Exception\EntityNotFoundException;
use MsgPhp\User\Command\{AddUserAttributeValueCommand, CreateUserCommand};
use MsgPhp\User\Entity\User;
use MsgPhp\User\Infra\Security\SecurityUserProvider;
use MsgPhp\User\Repository\{UserAttributeValueRepositoryInterface, UserRepositoryInterface};
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

final class OauthUserProvider implements OAuthAwareUserProviderInterface
{
    private $userRepository;
    private $userAttributeValueRepository;
    private $securityUserProvider;
    private $factory;
    private $commandBus;

    public function __construct(UserRepositoryInterface $userRepository, UserAttributeValueRepositoryInterface $userAttributeValueRepository, SecurityUserProvider $securityUserProvider, EntityFactoryInterface $factory, CommandBusInterface $commandBus)
    {
        $this->userRepository = $userRepository;
        $this->userAttributeValueRepository = $userAttributeValueRepository;
        $this->securityUserProvider = $securityUserProvider;
        $this->factory = $factory;
        $this->commandBus = $commandBus;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        if (null !== $username = $response->getUsername()) {
            /** @var UserAttributeValue|false $userAttributeValue */
            $userAttributeValue = $this->userAttributeValueRepository->findAllByAttributeIdAndValue(
                $attributeId = Attribute::getOauthId($owner = $response->getResourceOwner()->getName()),
                $username
            )->first();

            if (!$userAttributeValue) {
                if (null === $email = $response->getEmail()) {
                    throw new CustomUserMessageAuthenticationException(sprintf('Oauth resource owner "%s" requires e-mail availability/read-privilege.', $owner));
                }

                try {
                    $user = $this->userRepository->findByEmail($email);
                    $userId = $user->getId();
                } catch (EntityNotFoundException $e) {
                    $this->commandBus->handle(new CreateUserCommand($userId = $this->factory->nextIdentity(User::class), $email, bin2hex(random_bytes(8)), true));

                    $user = $this->userRepository->find($userId);
                }

                $this->commandBus->handle(new AddUserAttributeValueCommand($userId, $attributeId, $username));
            } else {
                $user = $userAttributeValue->getUser();
            }
        }

        return $this->securityUserProvider->create($user);
    }
}
