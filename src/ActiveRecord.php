<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord;

use Cycle\ActiveRecord\Internal\TransactionFacade;
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
     * Create a new entity instance with the given data.
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
     * Find a single record based on the given primary key.
     */
    final public static function findByPK(mixed $primaryKey): ?static
    {
        return static::query()->wherePK($primaryKey)->fetchOne();
    }

    /**
     * Find the first single record based on the given scope.
     *
     * @note Limit of 1 will be added to the query.
     */
    final public static function findOne(array $scope = []): ?static
    {
        return static::query()->fetchOne($scope);
    }

    /**
     * Find all records based on the given scope.
     *
     * @return iterable<static>
     */
    final public static function findAll(array $scope = []): iterable
    {
        return static::query()->where($scope)->fetchAll();
    }

    /**
     * Execute a callback within a single transaction.
     * Only {@see ActiveRecord} methods will be registered in the transaction and run on the callback completion.
     */
    public static function transact(callable $callback, TransactionMode $mode = TransactionMode::OpenNew): void
    {
        TransactionFacade::transact($callback, $mode);
    }

    /**
     * Get an ActiveQuery instance for the entity.
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
     * Persist the entity.
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
     * Persist the entity and throw an exception if an error occurs.
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

    /**
     * Prepare the entity for persistence.
     *
     * @note This function is experimental and may be removed in the future.
     */
    final public function persist(bool $cascade = true): EntityManagerInterface
    {
        return Facade::getEntityManager()->persist($this, $cascade);
    }

    /**
     * Delete the entity.
     *
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
     * Delete the entity and throw an exception if an error occurs.
     *
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
     * Prepare the entity for deletion.
     *
     * @note This function is experimental and may be removed in the future.
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
