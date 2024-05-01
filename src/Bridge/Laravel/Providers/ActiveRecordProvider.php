<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Bridge\Laravel\Providers;

use Cycle\ActiveRecord\Facade;
use Illuminate\Support\ServiceProvider;

class ActiveRecordProvider extends ServiceProvider
{
    public function register(): void
    {
        Facade::setContainer($this->app);
    }
}
