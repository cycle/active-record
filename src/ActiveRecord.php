<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord;

use Cycle\ActiveRecord\Exception\Transaction\TransactionException;
use Cycle\ActiveRecord\Internal\TransactionFacade;
use Cycle\ActiveRecord\Query\ActiveQuery;
use Cycle\Database\DatabaseInterface;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Exception\RunnerException;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;
use Cycle\ORM\SchemaInterface;

/**
 * A base class for entities that are managed by the ORM.
 * Adds a set of ActiveRecord methods to the extending entity class.
 */
abstract class ActiveRecord
{
    /**
     * Get the table name associated with the entity.
     *
     * @return non-empty-string
     */
    final public static function tableName(): string
    {
        return static::getOrm()->getSchema()->define(static::class, SchemaInterface::TABLE);
    }

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
     * Collects all the ActiveRecord operations within the callback and executes them
     * in a single {@see EntityManagerInterface} at the end of the callback.
     *
     * ```
     *  ActiveRecord::groupActions(static function (EntityManagerInterface $em) use ($user, $post): void {
     *      $user->saveOrFail();
     *      $post->saveOrFail();
     *  });
     * ```
     *
     * @note DBAL operations will not be collected and executed automatically
     *       within the EM transaction. Use {@see self::transact()} if you need to
     *       execute QueryBuilder and other DBAL operations within the same transaction.
     *
     * @note The difference between this method and {@see self::transact()} is that
     *       this method opens a new transaction only when all the operations were collected
     *       and are ready to be executed, while {@see self::transact()} opens a transaction
     *       immediately when the callback is called.
     *
     * @note Nested calls to this method will use separated Unit of Works, but transactions
     *       will be reused according to the given mode.
     *
     * @template TResult
     * @param callable(EntityManagerInterface): TResult $callback
     * @return TResult
     *
     * @throws TransactionException
     * @throws RunnerException
     * @throws \Throwable
     */
    final public static function groupActions(
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
     * All the ORM operations within the callback will be executed in the opened transaction without collecting.
     * If you need to collect ORM operations and execute them in a separated inner transaction,
     * use {@see self::groupActions()} within the callback.
     *
     * ```
     *  ActiveRecord::transact(function (DatabaseInterface $db, EntityManagerInterface $em) use ($service): void {
     *      $user = User::query()->forUpdate()->wherePK(1)->fetchOne();
     *      $service->process($user);
     *      // ORM action will be executed right away
     *      $user->save();
     *
     *     // DBAL action
     *      $db->getDriver()->execute('UPDATE some_table SET some_field = ? WHERE id = ?', ['value', 123]);
     *
     *      // EM executes action right away, you don't need to call $em->run()
     *      $em->persist(new Post('Title', 'Content'));
     *  });
     * ```
     *
     * @note If you call this method from a child class, the child database connection will be used for
     *       the transaction. If you call this method from the `ActiveRecord` class, the default database connection
     *       will be used.
     *
     * @template TResult
     * @param callable(DatabaseInterface, EntityManagerInterface): TResult $callback Note that the provided
     *        Entity Manager doesn't collect operations and executes them right away in the opened transaction.
     * @return TResult
     *
     * @throws TransactionException
     * @throws \Throwable
     */
    final public static function transact(
        callable $callback,
    ): mixed {
        return TransactionFacade::transact(
            $callback,
            self::getOrm()->getSchema()->defines(static::class) ? static::class : null,
        );
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
            return TransactionFacade::createEntityManager(TransactionMode::Ignore)
                ->persist($this, $cascade)
                ->run()
                ->isSuccess();
        }

        $transacting->persist($this, $cascade);
        return true;
    }

    /**
     * Persist the entity and throw an exception if an error occurs.
     * If the method is called inside a {@see self::groupActions()}, the exception WILL NOT be thrown.
     *
     * @throws \Throwable
     */
    final public function saveOrFail(bool $cascade = true): void
    {
        TransactionFacade::getEntityManager()
            ?->persist($this, $cascade) ?? TransactionFacade::createEntityManager(TransactionMode::Ignore)
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
            return TransactionFacade::createEntityManager(TransactionMode::Ignore)
                ->delete($this, $cascade)
                ->run()
                ->isSuccess();
        }

        $transacting->delete($this, $cascade);
        return true;
    }

    /**
     * Delete the entity and throw an exception if an error occurs.
     * If the method is called inside a {@see self::groupActions()}, the exception WILL NOT be thrown.
     *
     * @throws \Throwable
     */
    final public function deleteOrFail(bool $cascade = true): void
    {
        TransactionFacade::getEntityManager()
            ?->delete($this, $cascade) ?? TransactionFacade::createEntityManager(TransactionMode::Ignore)
            ->delete($this, $cascade)
            ->run();
    }

    private static function getOrm(): ORMInterface
    {
        return Facade::getOrm();
    }
}
