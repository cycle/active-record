<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord;

use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Transaction\StateInterface;
use Throwable;

/**
 * @template TEntity of ActiveRecord
 */
abstract class ActiveRecord
{
    /**
     * Finds a single record based on the given primary key.
     */
    final public static function find(mixed $primaryKey): ?static
    {
        /** @var static|null $entity */
        $entity = self::getRepository()->findByPK($primaryKey);

        return $entity;
    }

    /**
     * Finds a single record based on the given scope.
     */
    final public static function findOne(array $scope = []): ?static
    {
        /** @var static|null $entity */
        $entity = self::getRepository()->findOne($scope);

        return $entity;
    }

    /**
     * Finds all records based on the given scope.
     */
    final public static function findAll(array $scope = []): iterable
    {
        return self::getRepository()->findAll($scope);
    }

    /**
     * Returns a Select query builder for the extending entity class.
     *
     * @return Select<TEntity>
     */
    final public static function query(): Select
    {
        /** @var Select<TEntity> $select */
        $select = new Select(self::getOrm(), static::class);

        return $select;
    }

    private static function getRepository(): RepositoryInterface
    {
        return self::getOrm()->getRepository(static::class);
    }

    private static function getOrm(): ORMInterface
    {
        return Facade::getOrm();
    }

    /**
     * Persists the current entity.
     *
     * @throws Throwable
     */
    final public function save(bool $cascade = true): StateInterface
    {
        /** @var EntityManager $entityManager */
        $entityManager = Facade::getEntityManager();
        $entityManager->persist($this, $cascade);

        return $entityManager->run(throwException: false);
    }

    /**
     * Persists the current entity and throws an exception if an error occurs.
     *
     * @throws Throwable
     */
    final public function saveOrFail(bool $cascade = true): StateInterface
    {
        /** @var EntityManager $entityManager */
        $entityManager = Facade::getEntityManager();
        $entityManager->persist($this, $cascade);

        return $entityManager->run();
    }

    final public function persist(bool $cascade = true): EntityManagerInterface
    {
        return Facade::getEntityManager()->persist($this, $cascade);
    }

    /**
     * @throws Throwable
     */
    final public function delete(bool $cascade = true): StateInterface
    {
        /** @var EntityManager $entityManager */
        $entityManager = Facade::getEntityManager();
        $entityManager->delete($this, $cascade);

        return $entityManager->run(throwException: false);
    }

    /**
     * @throws Throwable
     */
    final public function deleteOrFail(bool $cascade = true): StateInterface
    {
        /** @var EntityManager $entityManager */
        $entityManager = Facade::getEntityManager();
        $entityManager->delete($this, $cascade);

        return $entityManager->run();
    }

    /**
     * Prepares the current entity for deletion.
     */
    final public function remove(bool $cascade = true): EntityManagerInterface
    {
        /** @var EntityManager $entityManager */
        $entityManager = Facade::getEntityManager();

        return $entityManager->delete($this, $cascade);
    }
}
