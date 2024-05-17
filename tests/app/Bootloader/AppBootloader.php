<?php

declare(strict_types=1);

namespace Cycle\App\Bootloader;

use Spiral\Bootloader\DomainBootloader;
use Spiral\Core\CoreInterface;
use Spiral\Cycle\Interceptor\CycleInterceptor;

final class AppBootloader extends DomainBootloader
{
    public function defineSingletons(): array
    {
        return [
            CoreInterface::class => [self::class, 'domainCore'],
        ];
    }

    protected static function defineInterceptors(): array
    {
        return [
            CycleInterceptor::class,
        ];
    }
}
