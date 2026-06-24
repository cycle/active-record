<?php

declare(strict_types=1);

namespace Cycle\Tests\Acceptance\Driver\Postgres;

use Cycle\Tests\Acceptance\Common\ActiveRecordTestCase;
use Testo\Filter\Group;
use Testo\Test;

/**
 * Runs the ActiveRecord acceptance scenarios against PostgreSQL.
 *
 * The driver is provisioned by {@see \Cycle\Tests\Acceptance\Testo\DatabaseInterceptor}, which
 * resolves it from the `driver-pgsql` group below. Skipped automatically when no server is reachable.
 */
#[Test]
#[Group('driver-pgsql')]
final class ActiveRecordTest extends ActiveRecordTestCase {}
