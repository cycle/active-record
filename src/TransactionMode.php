<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord;

use Cycle\ORM\Exception\RunnerException;

enum TransactionMode
{
    /**
     * Do nothing with transactions.
     *
     * @see \Cycle\ORM\Transaction\Runner::outerTransaction() with non-strict mode.
     */
    case Ignore;

    /**
     * The currently opened transaction will be used. If no transaction is opened, a {@see RunnerException}
     * exception will be thrown.
     *
     * @see \Cycle\ORM\Transaction\Runner::outerTransaction() with strict mode.
     */
    case Current;

    /**
     * A new transaction will be open for each used driver connection and will close they on finish.
     *
     * @see \Cycle\ORM\Transaction\Runner::innerTransaction()
     */
    case OpenNew;
}
