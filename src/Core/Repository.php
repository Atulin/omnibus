<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 07.09.2019, 23:51
 */

namespace Omnibus\Core;

use Omnibus\Models\Database;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;


class Repository
{
    /** @var EntityManager $em */
    protected $em;

    /** @var string|null ENTITY */
    protected const ENTITY = null;

    protected $errors = [];


    public function __construct()
    {
        $this->em = (new Database())->Get();
    }

    /**
     * @param string $alias
     * @param string|null $indexBy
     * @return QueryBuilder
     */
    protected function createQueryBuilder(string $alias, string $indexBy = null): QueryBuilder
    {
        return $this->em->createQueryBuilder()
            ->select($alias)
            ->from($this->getEntity(), $alias, $indexBy);
    }

    /**
     * @param string $alias
     * @param string|null $indexBy
     * @return QueryBuilder
     */
    protected function createCountQueryBuilder(string $alias, string $indexBy = null): QueryBuilder
    {
        return $this->em->createQueryBuilder()
            ->select("count($alias)")
            ->from($this->getEntity(), $alias, $indexBy);
    }

    /**
     * @param mixed $entity
     * @param bool  $flush
     *
     * @return array
     */
    public function save($entity, bool $flush = true): array
    {
        try {
            $this->em->persist($entity);
        } catch (ORMException $e) {
            $this->errors[] ='Could not create.';
        }
        if ($flush) {
            try {
                $this->em->flush();
            } catch (OptimisticLockException $e) {
                $this->errors[] = 'Could not create.';
            } catch (ORMException $e) {
                $this->errors[] = 'Could not create.';
            }
        }
        return array_unique($this->errors);
    }

    /**
     * @param mixed $entity
     * @param bool  $flush
     *
     * @return array
     */
    public function remove($entity, bool $flush = true): array
    {
        try {
            $this->em->remove($entity);
        } catch (ORMException $e) {
            $this->errors[] ='Could not remove.';
        }
        if ($flush) {
            try {
                $this->em->flush();
            } catch (OptimisticLockException $e) {
                $this->errors[] = 'Could not remove.';
            } catch (ORMException $e) {
                $this->errors[] = 'Could not remove.';
            }
        }
        return array_unique($this->errors);
    }

    /**
     * @param mixed $id
     *
     * @return null|object
     */
    public function find($id)
    {
        try {
            return $this->em->find($this->getEntity(), $id);
        } catch (OptimisticLockException $e) {
            $this->errors[] = 'Could not find.';
        } catch (TransactionRequiredException $e) {
            $this->errors[] = 'Could not find.';
        } catch (ORMException $e) {
            $this->errors[] = 'Could not find.';
        }
        return null;
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    protected function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->em->getRepository($this->getEntity())->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param mixed[] $criteria The criteria.
     * @return object|null The object.
     */
    protected function findOneBy(array $criteria)
    {
        return $this->em->getRepository($this->getEntity())->findOneBy($criteria);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->findBy([]);
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this::ENTITY;
    }

}
