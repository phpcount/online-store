<?php

namespace App\Utils\Manager;

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
    public function remove(object $entity, $withFlush = false)
    {
        if(!$entity->getProducts()->isEmpty())
            foreach ($entity->getProducts()->getValues() as $product) {
                parent::remove($product);
            }
        
        parent::remove($entity, $withFlush);
    }
}
