<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord;

use Cycle\ActiveRecord\Exception\Transaction\TransactionException;
use Cycle\ActiveRecord\Internal\TransactionFacade;
use Cycle\ActiveRecord\Query\ActiveQuery;
use Cycle\Database\DatabaseInterface;
use Cycle\ORM\Exception\RunnerException;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;

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
     *
     * All the ActiveRecord write operations within the callback will be registered
     * using the Entity Manager without being executed until the end of the callback.
     *
     * @note DBAL operations will not be executed within the transaction. Use {@see self::transact()} for that.
     *
     * @template TResult
     * @param callable(): TResult $callback
     * @return TResult
     *
     * @throws TransactionException
     * @throws RunnerException
     * @throws \Throwable
     */
    public static function groupActions(
        callable $callback,
        TransactionMode $mode = TransactionMode::OpenNew,
    ): mixed {
        return TransactionFacade::groupOrmActions($callback, $mode);
    }

    /**
     * Open a new DB transaction and execute the callback within it.
     *
     * All the DBAL operations within the callback will be executed within a single transaction.
     * If an exception is thrown within the callback, the transaction will be rolled back.
     * If the callback returns a value, the transaction will be committed.
     *
     * @template TResult
     * @param callable(DatabaseInterface): TResult $callback
     * @return TResult
     *
     * @throws TransactionException
     * @throws \Throwable
     */
    public static function transact(
        callable $callback,
    ): mixed {
        return TransactionFacade::transact($callback, static::class === self::class ? null : static::class);
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
     */
    final public function save(bool $cascade = true): bool
    {
        $transacting = TransactionFacade::getEntityManager();
        if ($transacting === null) {
            return Facade::getEntityManager()
                ->persist($this, $cascade)
                ->run(false)
                ->isSuccess();
        }

        $transacting->persist($this, $cascade);
        return true;
    }

    /**
     * Persist the entity and throw an exception if an error occurs.
     * The exception will be thrown if the action is happening not in a {@see self::transcat()} scope.
     *
     * @throws \Throwable
     */
    final public function saveOrFail(bool $cascade = true): void
    {
        TransactionFacade::getEntityManager()?->persist($this, $cascade) ?? Facade::getEntityManager()
            ->persist($this, $cascade)
            ->run();
    }

    /**
     * Delete the entity.
     */
    final public function delete(bool $cascade = true): bool
    {
        $transacting = TransactionFacade::getEntityManager();
        if ($transacting === null) {
            return Facade::getEntityManager()
                ->delete($this, $cascade)
                ->run(false)
                ->isSuccess();
        }

        $transacting->delete($this, $cascade);
        return true;
    }

    /**
     * Delete the entity and throw an exception if an error occurs.
     * The exception will be thrown if the action is happening not in a {@see self::transcat()} scope.
     *
     * @throws \Throwable
     */
    final public function deleteOrFail(bool $cascade = true): void
    {
        TransactionFacade::getEntityManager()?->delete($this, $cascade) ?? Facade::getEntityManager()
            ->delete($this, $cascade)
            ->run();
    }

    private static function getOrm(): ORMInterface
    {
        return Facade::getOrm();
    }
}
