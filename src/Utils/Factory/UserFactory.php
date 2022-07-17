<?php

namespace App\Utils\Factory;

use App\Entity\User;
use League\OAuth2\Client\Provider\GoogleUser;

class UserFactory
{
    public static function creteUserFromGoogleAccount(GoogleUser $googleUser): User
    {
        $user = new User();
        $user
            ->setEmail($googleUser->getEmail())
            ->setFullName($googleUser->getName())
            ->setGoogleId($googleUser->getId())
            ->setIsVerified(true)
        ;

        return $user;
    }
}
