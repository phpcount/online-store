<?php

namespace App\Utils\Manager;

use App\Entity\User;
use App\Exception\Security\EmptyUserPlainPasswordException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager extends AbstractBaseManager
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct($em);

        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @return void
     */
    public function hashPassword(User $user, string $plainPassword)
    {
        $plainPassword = trim($plainPassword);
        if (!$plainPassword) {
            throw new EmptyUserPlainPasswordException("Empty user's password");
        }

        $hashPassword = $this->userPasswordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashPassword);
    }

    public function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(User::class);
    }
}
