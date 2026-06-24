<?php

declare(strict_types=1);

namespace Cycle\Tests\Acceptance\Driver\SQLite;

use Cycle\Tests\Acceptance\Common\ActiveRecordTestCase;
use Testo\Filter\Group;
use Testo\Test;

/**
 * Runs the ActiveRecord acceptance scenarios against an in-memory SQLite database.
 *
 * The driver is provisioned by {@see \Cycle\Tests\Acceptance\Testo\DatabaseInterceptor}, which
 * resolves it from the `driver-sqlite` group below — the class needs no body of its own.
 */
#[Test]
#[Group('driver-sqlite')]
final class ActiveRecordTest extends ActiveRecordTestCase {}
