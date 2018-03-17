<?php

namespace App\Security;

use App\Entity\Eav\Attribute;
use App\Entity\User\User;
use App\Entity\User\UserAttributeValue;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use MsgPhp\Domain\Factory\EntityAwareFactoryInterface;
use MsgPhp\Domain\Exception\EntityNotFoundException;
use MsgPhp\User\Command\{AddUserAttributeValueCommand, ConfirmUserCommand, CreateUserCommand};
use MsgPhp\User\Infra\Security\SecurityUserProvider;
use MsgPhp\User\Repository\{UserAttributeValueRepositoryInterface, UserRepositoryInterface};
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

final class OauthUserProvider implements OAuthAwareUserProviderInterface
{
    private $userRepository;
    private $userAttributeValueRepository;
    private $securityUserProvider;
    private $factory;
    private $bus;

    public function __construct(UserRepositoryInterface $userRepository, UserAttributeValueRepositoryInterface $userAttributeValueRepository, SecurityUserProvider $securityUserProvider, EntityAwareFactoryInterface $factory, CommandBus $bus)
    {
        $this->userRepository = $userRepository;
        $this->userAttributeValueRepository = $userAttributeValueRepository;
        $this->securityUserProvider = $securityUserProvider;
        $this->factory = $factory;
        $this->bus = $bus;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $owner = $response->getResourceOwner()->getName();
        $username = $response->getUsername();

        $attributeId = $this->factory->identify(Attribute::class, Attribute::GOOGLE_OAUTH_ID);
        $userAttributeValue = $this->userAttributeValueRepository->findAllByAttributeIdAndValue($attributeId, $username)->first();

        if (!$userAttributeValue) {
            if (null === $email = $response->getEmail()) {
                throw new CustomUserMessageAuthenticationException(sprintf('Oauth resource owner "%s" requires e-mail availability and appropriate read-privilege.', $owner));
            }

            try {
                $user = $this->userRepository->findByUsername($email);
                $userId = $user->getId();
            } catch (EntityNotFoundException $e) {
                $userId = $this->factory->nextIdentifier(User::class);
                $this->bus->handle(new CreateUserCommand([
                    'id' => $userId,
                    'email' => $email,
                    'password' => bin2hex(random_bytes(32)),
                ]));
                $this->bus->handle(new ConfirmUserCommand($userId));

                $user = $this->userRepository->find($userId);
            }

            $this->bus->handle(new AddUserAttributeValueCommand($userId, $attributeId, $username));
        } else {
            /** @var UserAttributeValue $userAttributeValue */
            $user = $userAttributeValue->getUser();
        }

        return $this->securityUserProvider->fromUser($user);
    }
}
