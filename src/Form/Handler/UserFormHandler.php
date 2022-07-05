<?php

namespace App\Form\Handler;

use App\Entity\User;
use App\Utils\Manager\UserManager;
use Symfony\Component\Form\Form;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFormHandler
{

    /**
     *
     * @var UserManager
     */
    private $userManager;

    /**
     *
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;

    public function __construct(UserManager $userManager, UserPasswordHasherInterface $userPasswordHasher)
    {
       $this->userManager = $userManager;
       $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     *
     * @param Form $form
     * @return User
     */
    public function processEditForm(Form $form): User
    {
        /** @var User $user */
        $user = $form->getData();
    
        $plainPassword = $form->get('plainPassword')->getData();

        if ($plainPassword) {
            $hashedPassword = $this->userPasswordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }

        $this->userManager->save($user);

        return $user;
    }
}
