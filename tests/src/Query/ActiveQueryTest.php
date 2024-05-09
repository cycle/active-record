<?php

declare(strict_types=1);

namespace Cycle\Tests\Query;

use Cycle\ActiveRecord\Facade;
use Cycle\App\Entity\User;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager
use Cycle\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Test;
use ReflectionException;
use Throwable;

final class ActiveQueryTest extends DatabaseTestCase
{
    /**
     * @throws Throwable
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

    /**
     * @test
     *
     * @throws ReflectionException
     */
    #[Test]
    public function it_checks_if_fetch_all_method_returns_array_by_default(): void
    {
        $users = User::queryWithCollection()->fetchAll();

        dd($users);

        $this::assertIsArray($users);
    }
}
