<?php

namespace App\Utils\Manager;

use App\Utils\Manager\AbstractBaseManager;
use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;


class CartManager extends AbstractBaseManager
{

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
    }

    /**
     *
     * @return ObjectRepository
     */
    public function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(Cart::class);
    }

}