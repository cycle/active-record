<?php

declare(strict_types=1);

namespace Cycle\Tests\Acceptance\Testo;

use Cycle\Database\Config\DriverConfig;
use Cycle\Database\Config\MySQL\TcpConnectionConfig as MySQLConnection;
use Cycle\Database\Config\MySQLDriverConfig;
use Cycle\Database\Config\Postgres\TcpConnectionConfig as PostgresConnection;
use Cycle\Database\Config\PostgresDriverConfig;
use Cycle\Database\Config\SQLite\MemoryConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;
use Cycle\Database\Config\SQLServer\TcpConnectionConfig as SQLServerConnection;
use Cycle\Database\Config\SQLServerDriverConfig;

/**
 * The set of database drivers the acceptance suite can run against.
 *
 * The string value of each case mirrors the `driver-<value>` group used to select it on the CLI
 * (e.g. `--group=driver-mysql`); {@see DatabaseInterceptor} resolves the case back from that group.
 * Each case knows how to build the {@see DriverConfig} for its `default` connection — connection
 * parameters come from environment variables (matching the docker-compose setup) with sensible
 * local defaults.
 */
enum DatabaseDriver: string
{
    case SQLite = 'sqlite';
    case MySQL = 'mysql';
    case Postgres = 'pgsql';
    case SQLServer = 'sqlserver';

    /**
     * Resolve a driver from a `driver-<value>` group name, or null if the name is not a driver group.
     */
    public static function fromGroup(string $group): ?self
    {
        return \str_starts_with($group, 'driver-')
            ? self::tryFrom(\substr($group, \strlen('driver-')))
            : null;
    }

    /**
     * Build the driver configuration for this database's `default` connection.
     */
    public function defaultConfig(): DriverConfig
    {
        return match ($this) {
            self::SQLite => new SQLiteDriverConfig(
                connection: new MemoryConnectionConfig(),
                queryCache: true,
                options: ['logInterpolatedQueries' => true],
            ),
            self::MySQL => new MySQLDriverConfig(
                connection: new MySQLConnection(
                    database: self::env('DB_DATABASE', 'spiral'),
                    host: self::env('DB_HOSTNAME', '127.0.0.1'),
                    port: (int) self::env('DB_PORT', '13306'),
                    user: self::env('DB_USER', 'spiral'),
                    password: self::env('DB_PASSWORD', 'YourStrong!Passw0rd'),
                ),
                queryCache: true,
                options: ['logInterpolatedQueries' => true],
            ),
            self::Postgres => new PostgresDriverConfig(
                connection: new PostgresConnection(
                    database: self::env('DB_DATABASE', 'spiral'),
                    host: self::env('DB_HOSTNAME', '127.0.0.1'),
                    port: (int) self::env('DB_PORT', '15432'),
                    user: self::env('DB_USER', 'spiral'),
                    password: self::env('DB_PASSWORD', 'YourStrong!Passw0rd'),
                ),
                schema: 'public',
                queryCache: true,
                options: ['logInterpolatedQueries' => true],
            ),
            self::SQLServer => new SQLServerDriverConfig(
                connection: new SQLServerConnection(
                    database: 'tempdb',
                    host: self::env('DB_HOSTNAME', '127.0.0.1'),
                    port: (int) self::env('DB_PORT', '11433'),
                    trustServerCertificate: true,
                    user: 'SA',
                    password: self::env('DB_PASSWORD', 'YourStrong!Passw0rd'),
                ),
                queryCache: true,
                options: ['logInterpolatedQueries' => true],
            ),
        };
    }

    private static function env(string $name, string $default): string
    {
        $value = \getenv($name);

        return $value === false || $value === '' ? $default : $value;
    }
}
