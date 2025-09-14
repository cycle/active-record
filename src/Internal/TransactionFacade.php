<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Internal;

use Cycle\ActiveRecord\Exception\Transaction\TransactionException;
use Cycle\ActiveRecord\Facade;
use Cycle\ActiveRecord\TransactionMode;
use Cycle\Database\DatabaseInterface;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Service\SourceProviderInterface;
use Cycle\ORM\Transaction\Runner;
use Cycle\ORM\Transaction\UnitOfWork;
use Yiisoft\Injector\Injector;

/**
 * @internal
 */
final class TransactionFacade
{
    private static ?EntityManager $em = null;

    public static function getEntityManager(): ?EntityManagerInterface
    {
        return self::$em;
    }

    /**
     * Create a new EntityManager with its own UnitOfWork and Transaction Runner.
     */
    public static function createEntityManager(
        TransactionMode $mode = TransactionMode::OpenNew,
    ): EntityManagerInterface {
        return new EntityManager(
            static fn(): UnitOfWork => new UnitOfWork(Facade::getOrm(), self::getRunner($mode)),
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
            self::$em = new EntityManager(
                static fn(): UnitOfWork => new UnitOfWork(Facade::getOrm(), $runner),
            );
            $result = $callback(self::$em);
            self::$em->run();
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
     */
    public static function transact(
        callable $callback,
        ?string $entity,
    ): mixed {
        $dbal = $entity === null
            ? Facade::getDatabaseManager()->database()
            : Facade::getOrm()
                ->getService(SourceProviderInterface::class)
                ->getSource($entity)
                ->getDatabase();

        return $dbal->transaction(static function (DatabaseInterface $db) use ($callback): mixed {
            $previous = self::$em;
            try {
                $orm = Facade::getOrm();
                self::$em = $em = new EntityManager(
                    static fn(): UnitOfWork => new UnitOfWork($orm, Runner::outerTransaction(strict: true)),
                    autoExecute: true,
                );

                return (new Injector())->invoke($callback, [$db, $em, $orm, $orm->getHeap(), $orm->getSchema()]);
            } finally {
                self::$em = $previous;
            }
        });
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
