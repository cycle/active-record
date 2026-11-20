<?php

declare(strict_types=1);

use Cycle\Testing\Config;
use Cycle\Testing\Database\Isolation;
use Cycle\Testing\Database\Layout;
use Cycle\Testing\ORM\OrmPlugin;

return Config::forPackage('cycle/active-record', root: __DIR__)
    ->unit('tests/Unit')
    ->feature(
        'tests/Feature',
        isolation: Isolation::Transaction,
        layout: Layout::withSqlite(['secondary']),
        plugins: [new OrmPlugin()],
    )
    ->build();
