<?php

declare(strict_types=1);

namespace Cycle\Tests\Unit;

use Cycle\ActiveRecord\Exception\Transaction\TransactionException as DeprecatedTransactionException;
use Cycle\ActiveRecord\TransactionMode as DeprecatedTransactionMode;
use Cycle\Transaction\Exception\TransactionException;
use Cycle\Transaction\TransactionMode;
use Testo\Assert;
use Testo\Test;

/**
 * The names that moved to cycle/transaction still resolve to the new classes.
 */
#[Test]
final class DeprecatedAliasTest
{
    public function transactionModeResolvesToTheTransactionPackage(): void
    {
        Assert::same(DeprecatedTransactionMode::Current, TransactionMode::Current);
    }

    public function transactionExceptionResolvesToTheTransactionPackage(): void
    {
        Assert::same((new \ReflectionClass(DeprecatedTransactionException::class))->getName(), TransactionException::class);
    }
}
