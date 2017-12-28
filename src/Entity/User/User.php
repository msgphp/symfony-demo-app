<?php

namespace App\Entity\User;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;
use MsgPhp\User\Entity\User as BaseUser;
use MsgPhp\User\UserIdInterface;

/**
 * @ORM\Entity()
 *
 * @final
 */
class User extends BaseUser
{
    /**
     * @var Collection|UserRole[]
     * @ORM\OneToMany(targetEntity="UserRole", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $roles;

    public function __construct(UserIdInterface $id, string $email, string $password)
    {
        parent::__construct($id, $email, $password);

        $this->roles = new ArrayCollection();
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }

    public function addRole(string $role): void
    {
        if (!$this->hasRole($role)) {
            $this->roles->add(new UserRole($this, $role));
        }
    }

    public function removeRole(string $role): void
    {
        $this->roles->removeElement(
            $this->roles->filter(function (UserRole $userRole) use ($role) {
                return $userRole->getRole() === $role;
            })->first()
        );
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles->map(function (UserRole $userRole) {
            return $userRole->getRole();
        })->toArray();
    }
}
