<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Internal;

use Cycle\ActiveRecord\Exception\TransactionDesyncException;
use Cycle\ActiveRecord\Facade;
use Cycle\ActiveRecord\TransactionMode;
use Cycle\ORM\Transaction\Runner;
use Cycle\ORM\Transaction\StateInterface;
use Cycle\ORM\Transaction\UnitOfWork;

final class TransactionFacade
{
    private static ?UnitOfWork $uow = null;

    public static function getUoW(): ?UnitOfWork
    {
        return self::$uow;
    }

    public static function transact(
        callable $callable,
        TransactionMode $mode = TransactionMode::OpenNew,
    ): StateInterface {
        $previous = self::$uow;
        self::$uow = $uow = new UnitOfWork(
            Facade::getOrm(),
            match ($mode) {
                TransactionMode::Ignore => Runner::outerTransaction(strict: false),
                TransactionMode::Continue => Runner::outerTransaction(strict: true),
                TransactionMode::OpenNew => Runner::innerTransaction(),
            },
        );
        try {
            $callable();
            return $uow->run();
        } finally {
            self::$uow === $uow or throw new TransactionDesyncException(
                'A transaction was started outside of the previous transaction scope.',
            );
            self::$uow = $previous;
        }
    }
}
