<?php

declare(strict_types=1);

namespace Cycle\Tests;

use Cycle\Database\Driver\DriverInterface;

trait Loggable
{
    public static ?TestLogger $logger = null;

    protected function setUpLogger(DriverInterface $driver): static
    {
        static::$logger = static::$logger ?? new TestLogger();
        $driver->setLogger(static::$logger);

        return $this;
    }

    protected function enableProfiling(): void
    {
        static::$logger->display();
    }

    protected function disableProfiling(): void
    {
        static::$logger->hide();
    }
}
