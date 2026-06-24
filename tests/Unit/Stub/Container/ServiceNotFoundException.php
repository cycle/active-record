<?php

declare(strict_types=1);

namespace Cycle\Tests\Unit\Stub\Container;

use Psr\Container\NotFoundExceptionInterface;

/**
 * A concrete PSR-11 "not found" exception used by {@see ConfigurableContainer} to simulate a
 * container that has no binding for the requested service.
 *
 * @internal
 */
final class ServiceNotFoundException extends \RuntimeException implements NotFoundExceptionInterface {}
