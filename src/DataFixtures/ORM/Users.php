<?php

namespace App\DataFixtures\ORM;

use App\Entity\Eav\Attribute;
use App\Entity\Eav\AttributeValue;
use App\Entity\User\PremiumUser;
use App\Entity\User\Role;
use App\Entity\User\User;
use App\Entity\User\UserAttributeValue;
use App\Entity\User\UserEmail;
use App\Entity\User\UserRole;
use App\Security\UserRolesProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use MsgPhp\Domain\Factory\EntityAwareFactoryInterface;
use MsgPhp\User\Password\PasswordHashingInterface;

final class Users extends Fixture
{
    private const PASSWORD = 'pass';

    private $factory;
    private $passwordHashing;

    public function __construct(EntityAwareFactoryInterface $factory, PasswordHashingInterface $passwordHashing)
    {
        $this->factory = $factory;
        $this->passwordHashing = $passwordHashing;
    }

    public function load(ObjectManager $manager)
    {
        // roles
        $manager->persist($adminRole = new Role(UserRolesProvider::ROLE_ADMIN));

        // attributes
        $manager->persist($this->createAttribute(Attribute::GOOGLE_OAUTH_ID));
        $manager->persist($boolAttr = $this->createAttribute());
        $manager->persist($intAttr = $this->createAttribute());
        $manager->persist($floatAttr = $this->createAttribute());
        $manager->persist($stringAttr = $this->createAttribute());
        $manager->persist($dateTimeAttr = $this->createAttribute());

        // users
        $user = $this->createUser('user@domain.dev');
        $user->enable();
        $user->confirm();
        $manager->persist($user);
        $manager->persist(new UserEmail($user, 'other@domain.dev'));
        $manager->persist(new UserEmail($user, 'secondary@domain.dev', true));
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
        $user->confirm();
        $manager->persist($user);
        $manager->persist(new UserRole($user, $adminRole));

        $user = $this->createUser('user+admin+disabled@domain.dev');
        $manager->persist($user);
        $manager->persist(new UserRole($user, $adminRole));

        $premiumUser = $this->createPremiumUser('user+premium@domain.dev');
        $premiumUser->enable();
        $premiumUser->confirm();
        $manager->persist($premiumUser);

        $manager->flush();
    }

    private function createAttribute($id = null): Attribute
    {
        return new Attribute(null === $id ? $this->factory->nextIdentifier(Attribute::class) : $this->factory->identify(Attribute::class, $id));
    }

    private function createUser(string $email, string $password = self::PASSWORD, $class = User::class): User
    {
        return new $class($this->factory->nextIdentifier(User::class), $email, $this->passwordHashing->hash($password));
    }

    private function createPremiumUser(string $email, string $password = self::PASSWORD): PremiumUser
    {
        return $this->createUser($email, $password, PremiumUser::class);
    }

    private function createUserAttributeValue(User $user, Attribute $attribute, $value): UserAttributeValue
    {
        return new UserAttributeValue($user, new AttributeValue($this->factory->nextIdentifier(AttributeValue::class), $attribute, $value));
    }
}
