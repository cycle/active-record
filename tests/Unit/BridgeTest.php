<?php

declare(strict_types=1);

namespace Cycle\Tests\Unit;

use Cycle\ActiveRecord\Bridge\Laravel\Providers\ActiveRecordProvider;
use Cycle\ActiveRecord\Facade;
use Cycle\ORM\ORMInterface;
use Cycle\Testing\ORM\Memory;
use Cycle\Tests\Unit\Stub\Container\ConfigurableContainer;
use Testo\Assert;
use Testo\Lifecycle\AfterTest;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

/**
 * The Laravel provider and the Yii 3 bootstrap that composer.json registers for those frameworks.
 */
#[Test]
final class BridgeTest
{
    #[BeforeTest]
    #[AfterTest]
    public function resetFacade(): void
    {
        Facade::reset();
    }

    public function laravelProviderBindsTheApplicationIntoTheFacade(): void
    {
        $orm = Memory::orm();
        $app = ConfigurableContainer::returning(ORMInterface::class, $orm);

        (new ActiveRecordProvider($app))->register();

        Assert::same(Facade::getOrm(), $orm);
    }

    public function yiiBootstrapBindsTheContainerIntoTheFacade(): void
    {
        $orm = Memory::orm();
        $bootstrap = require \dirname(__DIR__, 2) . '/src/Bridge/Yii3/yii-bootstrap.php';

        $bootstrap['cycle-active-record'](ConfigurableContainer::returning(ORMInterface::class, $orm));

        Assert::same(Facade::getOrm(), $orm);
    }
}
