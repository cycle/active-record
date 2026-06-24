<?php

declare(strict_types=1);

namespace Cycle\Tests\Acceptance\Testo;

use Cycle\ActiveRecord\Facade;
use Cycle\Database\DatabaseManager;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Internal\Container\Container;
use Testo\Core\Context\CaseInfo;
use Testo\Core\Context\CaseResult;
use Testo\Core\Context\TestInfo;
use Testo\Core\Context\TestResult;
use Testo\Core\Exception\SkipTest;
use Testo\Core\Value\Status;
use Testo\Core\Value\TestType;
use Testo\Filter\Group;
use Testo\Pipeline\Attribute\InterceptorOptions;
use Testo\Pipeline\Middleware\TestCaseRunInterceptor;
use Testo\Pipeline\Middleware\TestRunInterceptor;

/**
 * Provisions the ActiveRecord ORM for the acceptance suite and isolates the tests from one another.
 *
 * Work is split between the two pipeline levels so nothing driver-specific is rebuilt per test:
 *
 * - {@see self::runTestCase()} runs once per case. It resolves the target driver from the `driver-*`
 *   group, creates the tables and seeds the data once per driver, then opens a Testo container
 *   {@see Container::scope()} in which it binds the freshly built ORM and the {@see DatabaseManager}.
 *   That scoped container is handed straight to the {@see Facade}; because the case (and all its tests)
 *   run inside the scope, `$this->container` resolves those bindings throughout.
 * - {@see self::runTest()} runs once per test. It pulls the ORM/DBAL back out of the (scoped)
 *   container, clears the ORM identity map and wraps the test in a database transaction that is rolled
 *   back afterwards — so tables and seeds are never recreated between tests, only the per-test changes
 *   are undone.
 *
 * Tests annotated with {@see WithoutTransaction} run without the wrapping transaction. If the target
 * database is unreachable the case scope is never opened, so its tests find no ORM bound and are
 * reported as {@see Status::Skipped} instead of failing the suite.
 */
#[InterceptorOptions(
    order: InterceptorOptions::ORDER_CLOSE_TO_TEST,
    testType: TestType::Test,
)]
final readonly class DatabaseInterceptor implements TestCaseRunInterceptor, TestRunInterceptor
{
    public function __construct(
        private Container $container,
        private ConnectionPool $pool,
        private SchemaInterface $schema,
    ) {}

    #[\Override]
    public function runTestCase(CaseInfo $info, callable $next): CaseResult
    {
        $class = $info->definition->reflection;
        $driver = $class === null ? null : self::resolveDriver($class);

        // A case without a driver group is not database-bound; run it untouched.
        if ($driver === null) {
            return $next($info);
        }

        $manager = $this->pool->manager($driver);

        try {
            $manager->database('default')->getDriver()->connect();
            $manager->database('secondary')->getDriver()->connect();
        } catch (\Throwable) {
            // Database unreachable: skip the scope so every test of this case is reported as skipped.
            return $next($info);
        }

        $this->prepare($driver, $manager);

        // Build the ORM inside a fresh container scope, bind it (and the DBAL) there and let the
        // Facade consume that very container. The scope — and its bindings — live only for this case.
        return $this->container->scope(function (Container $scope) use ($manager, $info, $next): CaseResult {
            $orm = OrmEnvironment::buildOrm($manager, $this->schema, $scope);

            $scope->set($orm, ORMInterface::class);
            $scope->set($manager);

            Facade::setContainer($scope);

            try {
                return $next($info);
            } finally {
                Facade::reset();
            }
        });
    }

    #[\Override]
    public function runTest(TestInfo $info, callable $next): TestResult
    {
        $class = $info->caseInfo->definition->reflection;
        $driver = $class === null ? null : self::resolveDriver($class);

        // Not a database-bound case.
        if ($driver === null) {
            return $next($info);
        }

        // No ORM in the current scope means the case scope was never opened — the database is down.
        if (!$this->container->has(ORMInterface::class)) {
            return new TestResult(
                info: $info,
                status: Status::Skipped,
                failure: new SkipTest(\sprintf('Database `%s` is not available.', $driver->value)),
            );
        }

        // Reuse the case ORM, but start each test with a clean identity map.
        $this->container->get(ORMInterface::class)->getHeap()->clean();

        $manager = $this->container->get(DatabaseManager::class);
        $default = $manager->database('default')->getDriver();
        $secondary = $manager->database('secondary')->getDriver();

        $wrap = !self::runsWithoutTransaction($info);
        if ($wrap) {
            $default->beginTransaction();
            $secondary->beginTransaction();
        }

        try {
            return $next($info);
        } finally {
            if ($wrap) {
                $secondary->rollbackTransaction();
                $default->rollbackTransaction();
            }
        }
    }

    /**
     * Find the first `driver-*` group declared on the test class or any of its ancestors.
     */
    private static function resolveDriver(\ReflectionClass $class): ?DatabaseDriver
    {
        for ($current = $class; $current !== false; $current = $current->getParentClass()) {
            foreach ($current->getAttributes(Group::class) as $attribute) {
                foreach ($attribute->newInstance()->names as $name) {
                    $driver = DatabaseDriver::fromGroup($name);
                    if ($driver !== null) {
                        return $driver;
                    }
                }
            }
        }

        return null;
    }

    private static function runsWithoutTransaction(TestInfo $info): bool
    {
        if ($info->testDefinition->reflection->getAttributes(WithoutTransaction::class) !== []) {
            return true;
        }

        $class = $info->caseInfo->definition->reflection;
        for ($current = $class; $current !== null && $current !== false; $current = $current->getParentClass()) {
            if ($current->getAttributes(WithoutTransaction::class) !== []) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create the tables and seed the baseline data for a driver, once per suite run.
     */
    private function prepare(DatabaseDriver $driver, DatabaseManager $manager): void
    {
        if ($this->pool->isPrepared($driver)) {
            return;
        }

        OrmEnvironment::purge($manager);
        OrmEnvironment::compileSchema($manager); // creates the tables on this connection
        OrmEnvironment::seed($manager);

        $this->pool->markPrepared($driver);
    }
}
