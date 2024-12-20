<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Internal;

use Cycle\ActiveRecord\Exception\Transaction\TransactionException;
use Cycle\ActiveRecord\Facade;
use Cycle\ActiveRecord\TransactionMode;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Transaction\Runner;

/**
 * @internal
 */
final class TransactionFacade
{
    private static ?EntityManagerInterface $em = null;

    public static function getEntityManager(): ?EntityManagerInterface
    {
        return self::$em;
    }

    /**
     * @template TResult
     * @param callable(): TResult $callback
     * @return TResult
     *
     * @throws TransactionException
     * @throws \Throwable
     */
    public static function groupOrmActions(
        callable $callback,
        TransactionMode $mode = TransactionMode::OpenNew,
    ): mixed {
        $runner = match ($mode) {
            TransactionMode::Ignore => Runner::outerTransaction(strict: false),
            TransactionMode::Current => Runner::outerTransaction(strict: true),
            TransactionMode::OpenNew => Runner::innerTransaction(),
        };

        self::$em === null or throw new TransactionException(
            'A transaction is already in progress.',
        );
        self::$em = Facade::getEntityManager();

        try {
            $result = $callback();
            self::$em->run(true, $runner);
            return $result;
        } finally {
            self::$em = null;
        }
    }
}
