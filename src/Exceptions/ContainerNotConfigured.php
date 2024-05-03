<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Exceptions;

use RuntimeException;

final class ContainerNotConfigured extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('The container is not configured.');
    }
}
