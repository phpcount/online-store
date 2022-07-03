<?php

namespace App\Utils\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

abstract class AbstractBaseManager
{

    /**
     *
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     *
     * @return ObjectRepository
     */
    abstract public function getRepository(): ObjectRepository;

    /**
     *
     * @param string $id
     * @return object|null
     */
    public function find(string $id): ?object
    {
        return $this->getRepository()->find($id);
    }

    /**
     *
     * @param object $entity
     * @return void
     */
    public function save(object $entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

   
    /**
     *
     * @param object $entity
     * @param boolean $withFlush
     * @return void
     */
    public function remove(object $entity, $withFlush = true)
    {
        if (method_exists($entity, 'setIsDeleted')) {
            $entity->setIsDeleted(true);
        } else {
            $this->em->remove($entity);
        }
        
        if ($withFlush)
            $this->em->flush();
    }
}
