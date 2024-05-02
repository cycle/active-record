<?php

declare(strict_types=1);

use Cycle\ActiveRecord\Facade;

return [
    'cycle-active-record' => Closure::fromCallable([Facade::class, 'setContainer']),
];
