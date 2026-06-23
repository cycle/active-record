<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord;

/**
 * IDE/static-analysis stub. It is never loaded at runtime — the real
 * {@see TransactionMode} is an alias of
 * {@see \Cycle\Transaction\TransactionMode} created in src/TransactionMode.php.
 *
 * @deprecated Use {@see \Cycle\Transaction\TransactionMode} instead.
 */
enum TransactionMode
{
    case Ignore;
    case Current;
    case OpenNew;
}
