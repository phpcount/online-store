<?php

namespace App\Utils\Manager;

use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class CartManager extends AbstractBaseManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
    }

    public function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(Cart::class);
    }
}
