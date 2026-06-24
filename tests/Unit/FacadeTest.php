<?php

declare(strict_types=1);

namespace Cycle\Tests\Unit;

use Cycle\ActiveRecord\Exception\ConfigurationException;
use Cycle\ActiveRecord\Facade;
use Cycle\Database\Config\SQLite\MemoryConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;
use Cycle\ORM\ORMInterface;
use Cycle\Tests\Acceptance\Testo\OrmEnvironment;
use Cycle\Tests\Unit\Stub\Container\ConfigurableContainer;
use Cycle\Tests\Unit\Stub\Container\ServiceNotFoundException;
use Testo\Assert;
use Testo\Codecov\Covers;
use Testo\Expect;
use Testo\Lifecycle\AfterTest;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

/**
 * Unit tests for the {@see Facade}, the static entry point ActiveRecord uses to reach the ORM.
 *
 * These exercise how the Facade talks to its PSR-11 container. Rather than mocking the container,
 * each test wires up a {@see ConfigurableContainer} with the precise behaviour under test, so the
 * assertions run against real container/exception objects.
 */
#[Test]
#[Covers(Facade::class)]
final class FacadeTest
{
    /**
     * The Facade keeps static state, so it must start and end every test with a clean slate —
     * otherwise a leftover container/ORM would leak between tests.
     */
    #[BeforeTest]
    #[AfterTest]
    public function resetFacade(): void
    {
        Facade::reset();
    }

    public function failsWhenContainerIsNotSet(): never
    {
        Expect::exception(ConfigurationException::class)
            ->withMessageContaining('Container has not been set.');

        Facade::getOrm();
    }

    public function getsOrmFromContainer(): void
    {
        $orm = $this->buildOrm();
        $container = ConfigurableContainer::returning(ORMInterface::class, $orm);

        Facade::setContainer($container);

        Assert::same(Facade::getOrm(), $orm);
        Assert::same($container->requested, [ORMInterface::class]);
    }

    public function throwsWhenContainerResolvesOrmToNull(): never
    {
        Facade::setContainer(new ConfigurableContainer(static fn(): null => null));

        Expect::exception(ConfigurationException::class)
            ->withMessageContaining('Failed to get ORMInterface from container.');

        Facade::getOrm();
    }

    public function throwsWhenContainerHasNoOrmService(): never
    {
        Facade::setContainer(new ConfigurableContainer(
            static fn(string $id): never => throw new ServiceNotFoundException("No `$id` service."),
        ));

        Expect::exception(ConfigurationException::class)
            ->withMessageContaining('Container has no ORMInterface service.')
            ->withPrevious(ServiceNotFoundException::class);

        Facade::getOrm();
    }

    private function buildOrm(): ORMInterface
    {
        return OrmEnvironment::forDriver(new SQLiteDriverConfig(connection: new MemoryConnectionConfig()));
    }
}
