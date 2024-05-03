<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord;

use Cycle\ORM\EntityManager;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Transaction\StateInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * @template TEntity of ActiveRecord
 */
abstract class ActiveRecord
{
    /**
     * Finds a single record based on the given primary key.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    final public static function find(mixed $primaryKey): ?static
    {
        /** @var static|null $entity */
        $entity = self::getRepository()->findByPK($primaryKey);

        return $entity;
    }

    /**
     * Finds a single record based on the given scope.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    final public static function findOne(array $scope = []): ?static
    {
        /** @var static|null $entity */
        $entity = self::getRepository()->findOne($scope);

        return $entity;
    }

    /**
     * Finds all records based on the given scope.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    final public static function findAll(array $scope = []): iterable
    {
        return self::getRepository()->findAll($scope);
    }

    /**
     * Returns a Select query builder for the extending entity class.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return Select<TEntity>
     */
    final public static function query(): Select
    {
        /** @var Select<TEntity> $select */
        $select = new Select(self::getOrm(), static::class);

        return $select;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    private static function getRepository(): RepositoryInterface
    {
        return self::getOrm()->getRepository(static::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private static function getOrm(): ORMInterface
    {
        return Facade::getOrm();
    }

    /**
     * Persists the current entity.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    final public function saveOrFail(bool $cascade = true): StateInterface
    {
        /** @var EntityManager $entityManager */
        $entityManager = Facade::getEntityManager();
        $entityManager->persist($this, $cascade);

        return $entityManager->run();
    }
}
