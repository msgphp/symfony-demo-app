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

    /**
     * @var Collection|UserSecondaryEmail[]
     * @ORM\OneToMany(targetEntity="UserSecondaryEmail", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $secondaryEmails;

    public function __construct(UserIdInterface $id, string $email, string $password)
    {
        parent::__construct($id, $email, $password);

        $this->roles = new ArrayCollection();
        $this->secondaryEmails = new ArrayCollection();
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

    public function getPendingPrimaryEmail(): ?string
    {
        return $this->secondaryEmails->filter(function (UserSecondaryEmail $userSecondaryEmail) {
            return $userSecondaryEmail->isPendingPrimary();
        })->map(function (UserSecondaryEmail $userSecondaryEmail) {
            return $userSecondaryEmail->getEmail();
        })->first() ?: null;
    }

    public function getSecondaryEmail(string $email): ?UserSecondaryEmail
    {
        return $this->secondaryEmails->filter(function (UserSecondaryEmail $userSecondaryEmail) use ($email) {
            return $userSecondaryEmail->getEmail() === $email;
        })->first() ?: null;
    }

    /**
     * @return Collection|UserSecondaryEmail
     */
    public function getSecondaryEmails(): Collection
    {
        return $this->secondaryEmails;
    }
}
