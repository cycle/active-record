<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord;

use Cycle\ActiveRecord\Query\ActiveQuery;
use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;
use Cycle\ORM\Transaction\StateInterface;

/**
 * A base class for entities that are managed by the ORM.
 * Adds a set of ActiveRecord methods to the extending entity class.
 */
abstract class ActiveRecord
{
    /**
     * Creates a new entity instance with the given data.
     * It is preferable to use this method instead of the constructor because
     * it uses ORM services to create the entity.
     *
     * @note Equals to calling {@see ORMInterface::make()}.
     *
     * Example:
     *
     * ```php
     * $user = User::make([
     *    'name' => 'John Doe',
     *    'email' => 'johndoe@example.com',
     * ]);
     * ```
     *
     * @param array<non-empty-string, mixed> $data An associative array where keys are property names
     *        and values are property values.
     */
    public static function make(array $data): static
    {
        return self::getOrm()->make(static::class, $data);
    }

    /**
     * Finds a single record based on the given primary key.
     */
    final public static function findByPK(mixed $primaryKey): ?static
    {
        return static::query()->wherePK($primaryKey)->fetchOne();
    }

    /**
     * Finds the first single record based on the given scope.
     *
     * @note Limit of 1 will be added to the query.
     */
    final public static function findOne(array $scope = []): ?static
    {
        return static::query()->fetchOne($scope);
    }

    /**
     * Finds all records based on the given scope.
     *
     * @return iterable<static>
     */
    final public static function findAll(array $scope = []): iterable
    {
        return static::query()->where($scope)->fetchAll();
    }

    /**
     * Returns a ActiveQuery query builder for the extending entity class.
     *
     * @return ActiveQuery<static>
     */
    public static function query(): ActiveQuery
    {
        return new ActiveQuery(static::class);
    }

    public static function getRepository(): RepositoryInterface
    {
        return self::getOrm()->getRepository(static::class);
    }

    /**
     * Persists the current entity.
     *
     * @throws \Throwable
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
     * @throws \Throwable
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
     * @throws \Throwable
     */
    final public function delete(bool $cascade = true): StateInterface
    {
        /** @var EntityManager $entityManager */
        $entityManager = Facade::getEntityManager();
        $entityManager->delete($this, $cascade);

        return $entityManager->run(throwException: false);
    }

    /**
     * @throws \Throwable
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

    private static function getOrm(): ORMInterface
    {
        return Facade::getOrm();
    }
}
