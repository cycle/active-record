<?php

declare(strict_types=1);

use Cycle\Database\Config;

return [
    'logger' => [
        'default' => null,
        'drivers' => [
            'memory' => 'sql_logs', // Log channel for Sql
        ],
    ],

    /*
     * Default database connection
     */
    'default' => 'default',

    /*
     * The Spiral/Database module provides support to manage multiple databases
     * in one application, use read/write connections and logically separate
     * multiple databases within one connection using prefixes.
     *
     * To register a new database simply add a new one into
     * "databases" section below.
     */
    'databases' => [
        'default' => [
            'driver' => env('DB_DRIVER', 'memory'),
        ],
    ],

    /*
     * Each database instance must have an associated connection object.
     * Connections used to provide low-level functionality and wrap different
     * database drivers. To register a new connection you have to specify
     * the driver class and its connection options.
     */
    'drivers' => [
        'memory' => new Config\SQLiteDriverConfig(
            connection: new Config\SQLite\MemoryConnectionConfig(),
            queryCache: true
        ),
        'sqlite' => new Config\SQLiteDriverConfig(
            queryCache: true,
        ),
        'mysql' => new Config\MySQLDriverConfig(
            connection: new Config\MySQL\TcpConnectionConfig(
                database: env('DB_DATABASE', 'default'),
                host: 'mysql',
                port: 3306,
                user: env('DB_USER', 'cycle'),
                password: env('DB_PASSWORD'),
            ),
            queryCache: true
        ),
        'pgsql' => new Config\PostgresDriverConfig(
            connection: new Config\Postgres\TcpConnectionConfig(
                database: env('DB_DATABASE', 'default'),
                host: 'pgsql',
                port: 5432,
                user: env('DB_USER', 'cycle'),
                password: env('DB_PASSWORD'),
            ),
            schema: 'public',
            queryCache: true,
        ),
        'sqlserver' => new Config\SQLServerDriverConfig(
            connection: new Config\SQLServer\TcpConnectionConfig(
                database: 'tempdb',
                host: 'sqlserver',
                port: 1433,
                user: 'SA',
                password: env('DB_PASSWORD')
            ),
            queryCache: true
        ),
    ],
];
