<?php

namespace App\Utils\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use App\Utils\Manager\AbstractBaseManager;
use App\Entity\Category;



class CategoryManager extends AbstractBaseManager
{
    public function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(Category::class);
    }

    /**
     *
     * @param Category $entity
     * @return void
     */
    public function remove(object $entity, $withFlush = true)
    {
        if(!$entity->getProducts()->isEmpty())
            /** @var Product $product */
            foreach ($entity->getProducts()->getValues() as $product) {
                parent::remove($product, false);
            }
        
        parent::remove($entity, $withFlush);
    }
}
