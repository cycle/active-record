<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Bridge\Laravel\Providers;

use Cycle\ActiveRecord\Facade;
use Illuminate\Support\ServiceProvider;

final class ActiveRecordProvider extends ServiceProvider
{
    /**
     * @psalm-external-mutation-free
     */
    #[\Override]
    public function register(): void
    {
        Facade::setContainer($this->app);
    }
}
