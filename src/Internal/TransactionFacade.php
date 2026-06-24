<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Internal;

use Cycle\Transaction\Exception\TransactionException;
use Cycle\ActiveRecord\Facade;
use Cycle\Database\DatabaseInterface;
use Cycle\ORM\EntityManager as ORMEntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Transaction\Runner;
use Cycle\Transaction\FlushMode;
use Cycle\Transaction\Internal\TransactionImpl;
use Cycle\Transaction\Transaction;
use Cycle\Transaction\TransactionMode;
use Yiisoft\Injector\Injector;

/**
 * @internal
 */
final class TransactionFacade
{
    private static ?EntityManagerInterface $em = null;

    /**
     * @psalm-external-mutation-free
     */
    public static function getEntityManager(): ?EntityManagerInterface
    {
        return self::$em;
    }

    /**
     * Persist a single entity in its own transaction, executing it immediately.
     *
     * @throws TransactionException
     * @throws \Throwable
     */
    public static function persist(object $entity, bool $cascade = true): void
    {
        self::getTransaction()->transact(
            static fn(EntityManagerInterface $em): EntityManagerInterface => $em->persist($entity, $cascade),
            self::resolveDatabaseName($entity),
        );
    }

    /**
     * Delete a single entity in its own transaction, executing it immediately.
     *
     * @throws TransactionException
     * @throws \Throwable
     */
    public static function delete(object $entity, bool $cascade = true): void
    {
        self::getTransaction()->transact(
            static fn(EntityManagerInterface $em): EntityManagerInterface => $em->delete($entity, $cascade),
            self::resolveDatabaseName($entity),
        );
    }

    /**
     * @template TResult
     * @param callable(EntityManagerInterface): TResult $callback
     * @return TResult
     *
     * @throws TransactionException
     * @throws \Throwable
     */
    public static function groupOrmActions(
        callable $callback,
        TransactionMode $mode = TransactionMode::OpenNew,
    ): mixed {
        $runner = self::getRunner($mode);

        $previous = self::$em;
        try {
            self::$em = $em = new ORMEntityManager(Facade::getOrm());
            $result = $callback($em);
            $em->run(runner: $runner);
            return $result;
        } finally {
            self::$em = $previous;
        }
    }

    /**
     * @template TResult
     * @param callable(): TResult $callback
     * @param class-string|null $entity If null, the default database will be used.
     * @psalm-param callable(...): TResult $callback
     * @return TResult
     *
     * @throws TransactionException
     * @throws \Throwable
     *
     * @psalm-suppress MixedReturnStatement The transaction callback yields a value Psalm cannot narrow.
     */
    public static function transact(
        callable $callback,
        ?string $entity,
    ): mixed {
        return self::getTransaction()->transact(
            callback: static function (EntityManagerInterface $em, DatabaseInterface $db) use ($callback): mixed {
                $previous = self::$em;
                self::$em = $em;
                try {
                    $orm = Facade::getOrm();
                    return (new Injector())->invoke($callback, [$db, $em, $orm, $orm->getHeap(), $orm->getSchema()]);
                } finally {
                    self::$em = $previous;
                }
            },
            source: $entity,
            emMode: TransactionMode::Current,
            flush: FlushMode::OnWrite,
        );
    }

    /**
     * Build a Transaction service backed by the ActiveRecord ORM and database provider.
     */
    private static function getTransaction(): Transaction
    {
        return new TransactionImpl(Facade::getOrm(), Facade::getDatabaseManager());
    }

    /**
     * Resolve the database name the given entity is stored in.
     *
     * Resolving from the instance (not its class) handles ORM proxies correctly.
     *
     * @return non-empty-string
     */
    private static function resolveDatabaseName(object $entity): string
    {
        $orm = Facade::getOrm();

        return $orm->getSource($orm->resolveRole($entity))->getDatabase()->getName();
    }

    /**
     * Create a transaction runner based on the provided mode.
     */
    private static function getRunner(TransactionMode $mode): Runner
    {
        return match ($mode) {
            TransactionMode::Ignore => Runner::outerTransaction(strict: false),
            TransactionMode::Current => Runner::outerTransaction(strict: true),
            TransactionMode::OpenNew => Runner::innerTransaction(),
        };
    }
}
