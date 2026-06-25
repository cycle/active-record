<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Bridge\Spiral\Bootloader;

use Cycle\ActiveRecord\Facade;
use Cycle\Transaction\Bridge\Spiral\Bootloader\TransactionBootloader;
use Psr\Container\ContainerInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Cycle\Bootloader\CycleOrmBootloader;

final class ActiveRecordBootloader extends Bootloader
{
    /**
     * @psalm-pure
     */
    #[\Override]
    public function defineDependencies(): array
    {
        return [
            CycleOrmBootloader::class,
            TransactionBootloader::class,
        ];
    }

    /**
     * @psalm-external-mutation-free
     */
    public function init(ContainerInterface $container): void
    {
        Facade::setContainer($container);
    }
}
