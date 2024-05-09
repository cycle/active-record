<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Collection
{
    public function __construct(public string $name)
    {
    }
}
