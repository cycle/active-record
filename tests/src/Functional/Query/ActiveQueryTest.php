<?php

declare(strict_types=1);

namespace Cycle\Tests\Functional\Query;

use Cycle\App\Entity\User;
use Cycle\Tests\Functional\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Test;

final class ActiveQueryTest extends DatabaseTestCase
{
    #[Test]
    public function it_gets_role_from_query(): void
    {
        $query = User::query();

        self::assertSame(User::class, $query->getRole());
    }
}
