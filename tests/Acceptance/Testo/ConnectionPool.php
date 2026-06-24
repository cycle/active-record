<?php

declare(strict_types=1);

namespace Cycle\Tests\Acceptance\Testo;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Config\DriverConfig;
use Cycle\Database\Config\SQLite\MemoryConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;
use Cycle\Database\DatabaseManager;

/**
 * A per-suite pool of {@see DatabaseManager} instances, one per {@see DatabaseDriver}.
 *
 * The plugin builds a single pool and shares it across every test of the suite, so the (potentially
 * expensive) connection to each real database is opened at most once per worker and then reused. The
 * pool also remembers which drivers have already had their schema and seed data prepared, so that
 * one-time setup runs only on the first test of each driver.
 */
final class ConnectionPool
{
    /** @var array<value-of<DatabaseDriver>, DatabaseManager> */
    private array $managers = [];

    /** @var array<value-of<DatabaseDriver>, true> */
    private array $prepared = [];

    /**
     * Build a manager whose `default` connection uses the given driver and whose `secondary`
     * connection is always an in-memory SQLite database (for driver-agnostic multi-database tests).
     */
    public static function createManager(DriverConfig $defaultDriver): DatabaseManager
    {
        return new DatabaseManager(new DatabaseConfig([
            'default' => 'default',
            'databases' => [
                'default' => ['connection' => 'default'],
                'secondary' => ['connection' => 'secondary'],
            ],
            'connections' => [
                'default' => $defaultDriver,
                'secondary' => new SQLiteDriverConfig(connection: new MemoryConnectionConfig()),
            ],
        ]));
    }

    public function manager(DatabaseDriver $driver): DatabaseManager
    {
        return $this->managers[$driver->value] ??= self::createManager($driver->defaultConfig());
    }

    public function isPrepared(DatabaseDriver $driver): bool
    {
        return isset($this->prepared[$driver->value]);
    }

    public function markPrepared(DatabaseDriver $driver): void
    {
        $this->prepared[$driver->value] = true;
    }
}
