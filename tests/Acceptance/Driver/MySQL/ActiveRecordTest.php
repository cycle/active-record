<?php

declare(strict_types=1);

namespace Cycle\Tests\Acceptance\Driver\MySQL;

use Cycle\Tests\Acceptance\Common\ActiveRecordTestCase;
use Testo\Filter\Group;
use Testo\Test;

/**
 * Runs the ActiveRecord acceptance scenarios against MySQL.
 *
 * The driver is provisioned by {@see \Cycle\Tests\Acceptance\Testo\DatabaseInterceptor}, which
 * resolves it from the `driver-mysql` group below. Skipped automatically when no server is reachable.
 */
#[Test]
#[Group('driver-mysql')]
final class ActiveRecordTest extends ActiveRecordTestCase {}
