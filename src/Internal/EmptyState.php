<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Internal;

use Cycle\ORM\Transaction\StateInterface;

/**
 * Empty transaction state.
 *
 * @internal
 */
final class EmptyState implements StateInterface
{

    public function __construct(
        private readonly ?\Throwable $error = null,
    ) {}

    public function isSuccess(): bool
    {
        return $this->error !== null;
    }

    public function getLastError(): ?\Throwable
    {
        return $this->error;
    }

    public function retry(): StateInterface
    {
        return $this;
    }
}
