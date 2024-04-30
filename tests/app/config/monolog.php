<?php

declare(strict_types=1);

use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Level;

return [
    'globalLevel' => Level::Debug,
    'handlers' => [
        'console' => [
            [
                'class' => SyslogHandler::class,
                'options' => [
                    'ident' => 'spiral-app',
                ],
            ],
        ],
        'sql_logs' => [
            [
                'class' => RotatingFileHandler::class,
                'options' => [
                    'filename' => __DIR__ . '/../../runtime/logs/sql.log',
                ],
            ],
        ],
    ],
];
