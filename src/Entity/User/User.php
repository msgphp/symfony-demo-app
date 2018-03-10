<?php

namespace App\Entity\User;

use App\Entity\Eav\Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MsgPhp\Domain\Entity\Features\CanBeConfirmed;
use MsgPhp\Domain\Entity\Features\CanBeEnabled;
use MsgPhp\Domain\Entity\Fields\CreatedAtField;
use MsgPhp\Domain\Event\DomainEventHandlerInterface;
use MsgPhp\Domain\Event\DomainEventHandlerTrait;
use MsgPhp\User\Entity\Credential\EmailPassword;
use MsgPhp\User\Entity\Features\EmailPasswordCredential;
use MsgPhp\User\Entity\Features\ResettablePassword;
use MsgPhp\User\Entity\Fields\EmailsField;
use MsgPhp\User\Entity\User as BaseUser;
use MsgPhp\User\UserIdInterface;

/**
 * @ORM\Entity()
 *
 * @final
 */
class User extends BaseUser implements DomainEventHandlerInterface
{
    use CreatedAtField;
    use EmailPasswordCredential;
    use ResettablePassword;
    use CanBeEnabled;
    use CanBeConfirmed;
    use EmailsField;
    use DomainEventHandlerTrait;

    /** @ORM\Id @ORM\Column(type="msgphp_user_id") */
    private $id;

    /**
     * @var Collection|UserRole[]
     * @ORM\OneToMany(targetEntity="UserRole", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $roles;

    /**
     * @var Collection|UserAttributeValue[]
     * @ORM\OneToMany(targetEntity="UserAttributeValue", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $attributeValues;

    public function __construct(UserIdInterface $id, string $email, string $password)
    {
        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
        $this->credential = new EmailPassword($email, $password);
        $this->confirmationToken = bin2hex(random_bytes(32));
        $this->roles = new ArrayCollection();
        $this->attributeValues = new ArrayCollection();
    }

    public function getId(): UserIdInterface
    {
        return $this->id;
    }

    public function getRole(string $role): ?UserRole
    {
        return $this->roles->filter(function (UserRole $userRole) use ($role) {
            return $userRole->getRole() === $role;
        })->first() ?: null;
    }

    /**
     * @return Collection|UserRole[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @return Collection|UserAttributeValue[]
     */
    public function getAttributeValues(Attribute $attribute = null): Collection
    {
        if (null === $attribute) {
            return $this->attributeValues;
        }

        return $this->attributeValues->filter(function (UserAttributeValue $userAttributeValue) use ($attribute) {
            return $userAttributeValue->getAttributeId()->equals($attribute->getId());
        });
    }
}
