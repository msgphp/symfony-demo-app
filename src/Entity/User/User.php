<?php

namespace App\Entity\User;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use MsgPhp\Domain\Entity\Features\CanBeConfirmed;
use MsgPhp\Domain\Entity\Features\CanBeEnabled;
use MsgPhp\Domain\Entity\Fields\CreatedAtField;
use MsgPhp\Domain\Event\DomainEventHandlerInterface;
use MsgPhp\Domain\Event\DomainEventHandlerTrait;
use MsgPhp\User\Entity\Credential\EmailPassword;
use MsgPhp\User\Entity\Features\EmailPasswordCredential;
use MsgPhp\User\Entity\Features\ResettablePassword;
use MsgPhp\User\Entity\Fields\AttributeValuesField;
use MsgPhp\User\Entity\Fields\EmailsField;
use MsgPhp\User\Entity\Fields\RolesField;
use MsgPhp\User\Entity\User as BaseUser;
use MsgPhp\User\UserIdInterface;

/**
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"user" = "User", "premium_user" = "PremiumUser"})
 * @ApiResource(shortName="DbUser")
 * @ApiFilter(SearchFilter::class, properties={"credential.email": "partial"})
 */
class User extends BaseUser implements DomainEventHandlerInterface
{
    use CreatedAtField;
    use EmailPasswordCredential;
    use ResettablePassword;
    use CanBeEnabled;
    use CanBeConfirmed;
    use EmailsField;
    use RolesField;
    use AttributeValuesField;
    use DomainEventHandlerTrait;

    /** @ORM\Id() @ORM\Column(type="msgphp_user_id") @ApiProperty(identifier=true) */
    private $id;

    public function __construct(UserIdInterface $id, string $email, string $password)
    {
        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
        $this->credential = new EmailPassword($email, $password);
        $this->confirmationToken = bin2hex(random_bytes(32));
    }

    public function getId(): UserIdInterface
    {
        return $this->id;
    }
}
