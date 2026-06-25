<?php

declare(strict_types=1);

namespace Cycle\Tests\Acceptance\Testo;

use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Internal\Container\Container;
use Testo\Common\Messenger;
use Testo\Common\PluginConfigurator;
use Testo\Pipeline\InterceptorCollector;

/**
 * Testo plugin that provisions a Cycle ORM database for the acceptance suite.
 *
 * The ORM schema is driver-agnostic, so it is compiled exactly once — here, when the suite is
 * configured — and stored in the suite container under {@see SchemaInterface}. {@see DatabaseInterceptor}
 * then reuses it to build a fresh ORM per test (cheap), creating the physical tables and seeding the
 * data once per driver and wrapping each test in a rolled-back transaction for isolation.
 *
 * Wire it into the Acceptance suite in `testo.php`:
 *
 * ```
 * new SuiteConfig(
 *     name: 'Acceptance',
 *     location: new FinderConfig(include: ['tests/Acceptance/Driver']),
 *     plugins: [new DatabasePlugin()],
 * );
 * ```
 */
final readonly class DatabasePlugin implements PluginConfigurator
{
    #[\Override]
    public function configure(Container $container): void
    {
        // Compile the schema once (it is the same for every driver) and share it through the
        // container. An in-memory SQLite connection is enough to compile against.
        $schema = new Schema(OrmEnvironment::compileSchema(
            ConnectionPool::createManager(DatabaseDriver::SQLite->defaultConfig()),
        ));
        $container->set($schema, SchemaInterface::class);

        $pool = new ConnectionPool();
        $container->set($pool);
        $messenger = $container->get(Messenger::class);

        $container
            ->get(InterceptorCollector::class)
            ->addInterceptor(new DatabaseInterceptor($container, $pool, $schema, $messenger));
    }
}
