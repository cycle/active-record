<?php

declare(strict_types=1);

use Cycle\Testing\Config;
use Cycle\Testing\Database\Isolation;
use Cycle\Testing\Database\Layout;
use Cycle\Testing\ORM\OrmPlugin;
use Cycle\Testing\ORM\SchemaSource;
use Cycle\Tests\Stub\OrmEnvironment;

return Config::forPackage('cycle/active-record', root: __DIR__)
    ->unit('tests/Unit')
    ->acceptance(
        'tests/Acceptance',
        # The tables and the seed are created once per suite; each test is rolled back.
        isolation: Isolation::Transaction,
        layout: Layout::withSqlite(['secondary']),
        prepare: OrmEnvironment::prepare(...),
        plugins: [new OrmPlugin(
            schema: SchemaSource::compiled(OrmEnvironment::compileSchema(...)),
            factory: OrmEnvironment::buildOrm(...),
            bind: OrmEnvironment::bindFacade(...),
        )],
    )
    ->build();
