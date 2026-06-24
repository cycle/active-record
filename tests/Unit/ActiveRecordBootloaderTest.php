<?php

declare(strict_types=1);

namespace Cycle\Tests\Unit;

use Cycle\ActiveRecord\Bridge\Spiral\Bootloader\ActiveRecordBootloader;
use Cycle\ActiveRecord\Facade;
use Cycle\Database\Config\SQLite\MemoryConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;
use Cycle\ORM\ORMInterface;
use Cycle\Tests\Acceptance\Testo\OrmEnvironment;
use Cycle\Tests\Unit\Stub\Container\ConfigurableContainer;
use Spiral\Cycle\Bootloader\CycleOrmBootloader;
use Testo\Assert;
use Testo\Codecov\Covers;
use Testo\Lifecycle\AfterTest;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

/**
 * Unit tests for the Spiral {@see ActiveRecordBootloader}.
 *
 * Rather than booting a full Spiral application, these check the bootloader's own contract directly:
 * it hands the application container to the {@see Facade}, and it pulls in the Cycle ORM bootloader so
 * that an {@see ORMInterface} is available to resolve.
 */
#[Test]
#[Covers(ActiveRecordBootloader::class)]
final class ActiveRecordBootloaderTest
{
    #[BeforeTest]
    #[AfterTest]
    public function resetFacade(): void
    {
        Facade::reset();
    }

    public function itBindsTheContainerIntoTheFacade(): void
    {
        $orm = OrmEnvironment::forDriver(new SQLiteDriverConfig(connection: new MemoryConnectionConfig()));
        $container = ConfigurableContainer::returning(ORMInterface::class, $orm);

        (new ActiveRecordBootloader())->init($container);

        Assert::same(Facade::getOrm(), $orm);
    }

    public function itDependsOnCycleOrmBootloader(): void
    {
        Assert::contains((new ActiveRecordBootloader())->defineDependencies(), CycleOrmBootloader::class);
    }
}
