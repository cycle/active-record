<?php

declare(strict_types=1);

use Cycle\Tests\Acceptance\Testo\DatabasePlugin;
use Testo\Application\Config\ApplicationConfig;
use Testo\Application\Config\FinderConfig;
use Testo\Application\Config\SuiteConfig;
use Testo\Codecov\CodecovPlugin;
use Testo\Codecov\Config\CoverageLevel;
use Testo\Codecov\Config\CoverageMode;
use Testo\Codecov\Report\CloverReport;

return new ApplicationConfig(
    src: ['src'],
    suites: [
        // Driver-agnostic tests that need no real database connection.
        new SuiteConfig(name: 'Unit', location: ['tests/Unit']),

        // Acceptance tests run against real database drivers. The abstract base classes live in
        // `tests/Acceptance/Common` and are extended by per-driver concrete classes under
        // `tests/Acceptance/Driver`. Only the concrete subclasses are discovered as test cases;
        // pick a driver at run time with `--group=driver-sqlite` (see the `test:*` composer scripts).
        //
        // The DatabasePlugin owns a connection pool for the whole suite and, around every test,
        // builds a clean ORM on the right driver and binds it to the ActiveRecord Facade.
        new SuiteConfig(
            name: 'Acceptance',
            location: new FinderConfig(include: ['tests/Acceptance/Driver']),
            plugins: [new DatabasePlugin()],
        ),
    ],
    plugins: [
        // Coverage is collected only when `--coverage` (or a report flag) is passed and an Xdebug/PCOV
        // driver is available; the Clover report is uploaded to Codecov by CI (see `composer test:cc`).
        new CodecovPlugin(
            level: CoverageLevel::Line,
            collect: CoverageMode::Never,
            reports: [
                new CloverReport(__DIR__ . '/runtime/clover.xml', 'cycle/active-record'),
            ],
        ),
    ],
);
