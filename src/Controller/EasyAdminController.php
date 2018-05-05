<?php

namespace App\Controller;

use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController;
use MsgPhp\Domain\Factory\EntityAwareFactoryInterface;

final class EasyAdminController extends AdminController
{
    private $factory;

    public function __construct(EntityAwareFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    protected function createNewEntity()
    {
        $fields = ['id' => $this->factory->nextIdentifier($class = $this->entity['class'])];

        switch ($class) {
            case User::class:
                $fields['email'] = '';
                $fields['password'] = '';
                break;
        }

        return $this->factory->create($class, $fields);
    }
}
