<?php

declare(strict_types=1);

namespace Cycle\Tests\Query;

use Cycle\ActiveRecord\Facade;
use Cycle\App\Entity\User;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Cycle\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Test;

final class ActiveQueryTest extends DatabaseTestCase
{
    /**
     * @test
     */
    #[Test]
    public function it_gets_role_from_query(): void
    {
        $query = User::query();

        self::assertSame(User::class, $query->getRole());
    }

    /**
     * @throws \Throwable
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $databaseManager = $this->getContainer()->get(DatabaseManager::class);
        /** @var Database $database */
        $database = $databaseManager->database('default');

        $this->dropDatabase($database);
        Facade::reset();
    }
}
