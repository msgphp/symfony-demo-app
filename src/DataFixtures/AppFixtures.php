<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Eav\Attribute;
use App\Entity\Eav\AttributeValue;
use App\Entity\User\PremiumUser;
use App\Entity\User\Role;
use App\Entity\User\User;
use App\Entity\User\UserAttributeValue;
use App\Entity\User\UserEmail;
use App\Entity\User\UserRole;
use App\Security\RoleProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use MsgPhp\Eav\Infra\Uuid\AttributeId;
use MsgPhp\Eav\Infra\Uuid\AttributeValueId;
use MsgPhp\User\Infra\Uuid\UserId;
use MsgPhp\User\Password\PasswordHashingInterface;

final class AppFixtures extends Fixture
{
    private const PASSWORD = 'pass';

    private $passwordHashing;

    public function __construct(PasswordHashingInterface $passwordHashing)
    {
        $this->passwordHashing = $passwordHashing;
    }

    public function load(ObjectManager $manager): void
    {
        // roles
        $manager->persist($adminRole = new Role(RoleProvider::ROLE_ADMIN));

        // attributes
        $manager->persist($this->createAttribute(Attribute::GOOGLE_OAUTH_ID));
        $manager->persist($this->createAttribute(Attribute::FACEBOOK_OAUTH_ID));
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

        $premiumUser = $this->createUser('user+premium@domain.dev', true);
        $premiumUser->enable();
        $premiumUser->confirm();
        $manager->persist($premiumUser);

        $manager->flush();
    }

    private function createAttribute($id = null): Attribute
    {
        return new Attribute(AttributeId::fromValue($id));
    }

    private function createUser(string $email, $premium = false, string $password = self::PASSWORD): User
    {
        $password = $this->passwordHashing->hash($password);

        if ($premium) {
            return new PremiumUser(new UserId(), $email, $password);
        }

        return new User(new UserId(), $email, $password);
    }

    private function createUserAttributeValue(User $user, Attribute $attribute, $value): UserAttributeValue
    {
        return new UserAttributeValue($user, new AttributeValue(new AttributeValueId(), $attribute, $value));
    }
}
