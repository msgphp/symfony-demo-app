<?php

namespace App\DataFixtures\ORM;

use App\Entity\Eav\{Attribute, AttributeValue};
use App\Entity\User\{User, UserAttributeValue, UserRole};
use App\Security\UserRolesProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use MsgPhp\Eav\Infra\Uuid\{AttributeId, AttributeValueId};
use MsgPhp\User\Infra\Uuid\UserId;
use MsgPhp\User\Password\PasswordHashingInterface;

final class Users extends Fixture
{
    private const PASSWORD = 'pass';

    private $passwordHashing;

    public function __construct(PasswordHashingInterface $passwordHashing)
    {
        $this->passwordHashing = $passwordHashing;
    }

    public function load(ObjectManager $manager)
    {
        $manager->persist(new Attribute(Attribute::getOauthId('google')));
        $manager->persist($boolAttr = new Attribute(new AttributeId()));
        $manager->persist($intAttr = new Attribute(new AttributeId()));
        $manager->persist($floatAttr = new Attribute(new AttributeId()));
        $manager->persist($stringAttr = new Attribute(new AttributeId()));
        $manager->persist($dateTimeAttr = new Attribute(new AttributeId()));

        $manager->flush();

        $user = $this->createUser('user@domain.dev');
        $user->enable();
        $manager->persist($user);
        $manager->persist($this->createUserAttributeValue($user, $boolAttr, true));
        $manager->persist($this->createUserAttributeValue($user, $boolAttr, false));
        $manager->persist($this->createUserAttributeValue($user, $boolAttr, null));
        $manager->persist($this->createUserAttributeValue($user, $intAttr, 123));
        $manager->persist($this->createUserAttributeValue($user, $intAttr, -456));
        $manager->persist($this->createUserAttributeValue($user, $floatAttr, 123.0123456789));
        $manager->persist($this->createUserAttributeValue($user, $floatAttr, -0.123));
        $manager->persist($this->createUserAttributeValue($user, $stringAttr, 'text'));
        $manager->persist($this->createUserAttributeValue($user, $dateTimeAttr, new \DateTimeImmutable()));

        $user = $this->createUser('user+disabled@domain.dev');
        $manager->persist($user);

        $user = $this->createUser('user+admin@domain.dev');
        $user->enable();
        $manager->persist($user);
        $manager->persist(new UserRole($user, UserRolesProvider::ROLE_ADMIN));

        $user = $this->createUser('user+admin+disabled@domain.dev');
        $manager->persist($user);
        $manager->persist(new UserRole($user, UserRolesProvider::ROLE_ADMIN));

        $manager->flush();
    }

    private function createUser(string $email, string $password = self::PASSWORD): User
    {
        return new User(new UserId(), $email, $this->passwordHashing->hash($password));
    }

    private function createUserAttributeValue(User $user, Attribute $attribute, $value): UserAttributeValue
    {
        return new UserAttributeValue($user, new AttributeValue(new AttributeValueId(), $attribute, $value));
    }
}
