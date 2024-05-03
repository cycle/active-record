<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Exceptions;

use RuntimeException;

final class ORMCarrierNotConfigured extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('The ORM Carrier is not configured.');
    }
}
