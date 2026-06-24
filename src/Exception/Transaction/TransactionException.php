<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Exception\Transaction;

/** @psalm-suppress DeprecatedClass The alias intentionally targets the deprecated class name. */
\class_alias(\Cycle\Transaction\Exception\TransactionException::class, TransactionException::class);
