<?php

declare(strict_types=1);

namespace Cycle\Tests\Functional;

use Cycle\ActiveRecord\Bridge\Spiral\Bootloader\ActiveRecordBootloader;
use Cycle\App\Bootloader\AppBootloader;
use Spiral\Boot\Bootloader\ConfigurationBootloader;
use Spiral\Monolog\Bootloader\MonologBootloader;

class TestCase extends \Spiral\Testing\TestCase
{
    public function rootDirectory(): string
    {
        return __DIR__ . '/../../';
    }

    public function defineBootloaders(): array
    {
        return [
            ConfigurationBootloader::class,
            ActiveRecordBootloader::class,
            AppBootloader::class,
            MonologBootloader::class,
        ];
    }
}
