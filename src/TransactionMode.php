<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord;

enum TransactionMode
{
    /**
     * Do nothing with transactions.
     *
     * @see \Cycle\ORM\Transaction\Runner::innerTransaction() with non-strict mode.
     */
    case Ignore;
    /**
     * The currently opened transaction will be used. If no transaction is opened, a new one will be created.
     *
     * @see \Cycle\ORM\Transaction\Runner::innerTransaction() with strict mode.
     */
    case Current;
    /**
     * A new transaction will be open for each used driver connection and will close they on finish.
     *
     * @see \Cycle\ORM\Transaction\Runner::innerTransaction()
     */
    case OpenNew;
}
