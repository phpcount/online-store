<?php

namespace App\Utils\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
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
     * @return QueryBuilder
     */
    public function createQueryBuilder(): QueryBuilder
    {
        return $this->em->createQueryBuilder();
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
        if (property_exists($entity, 'updatedAt')) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
        }
        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     *
     * @param object $entity
     * @param boolean $withFlush
     * @return void
     */
    public function remove(object $entity, $withFlush = false)
    {
        if (property_exists($entity, 'isDeleted')) {
            $entity->setIsDeleted(true);
        } else {
            $this->em->remove($entity);
        }

        if ($withFlush)
            $this->em->flush();
    }
}
