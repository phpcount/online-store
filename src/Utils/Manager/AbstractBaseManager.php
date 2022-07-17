<?php

namespace App\Utils\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;

abstract class AbstractBaseManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->em->createQueryBuilder();
    }

    abstract public function getRepository(): ObjectRepository;

    public function find(string $id): ?object
    {
        return $this->getRepository()->find($id);
    }

    /**
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
     * @param bool $withFlush
     *
     * @return void
     */
    public function remove(object $entity, $withFlush = false)
    {
        if (property_exists($entity, 'isDeleted')) {
            $entity->setIsDeleted(true);
        } else {
            $this->em->remove($entity);
        }

        if ($withFlush) {
            $this->em->flush();
        }
    }
}
