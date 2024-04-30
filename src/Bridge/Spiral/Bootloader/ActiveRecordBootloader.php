<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Bridge\Spiral\Bootloader;

use Cycle\ActiveRecord\Facade;
use Psr\Container\ContainerInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Cycle\Bootloader\CycleOrmBootloader;

class ActiveRecordBootloader extends Bootloader
{
    public function defineDependencies(): array
    {
        return [
            CycleOrmBootloader::class,
        ];
    }

    public function init(ContainerInterface $container): void
    {
        Facade::setContainer($container);
    }
}
