<?php

namespace App\Utils\Manager;

use Doctrine\Persistence\ObjectRepository;
use App\Utils\Manager\AbstractBaseManager;
use App\Entity\User;

class UserManager extends AbstractBaseManager
{
    public function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(User::class);
    }

}
