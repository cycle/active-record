<?php

declare(strict_types=1);

namespace Cycle\Tests;

use Cycle\ActiveRecord\Facade;
use Cycle\App\Entity\User;
use Cycle\Database\DatabaseManager;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

final class ActiveRecordTest extends DatabaseTestCase
{
    /**
     * @throws Throwable
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $databaseManager = $this->getContainer()->get(DatabaseManager::class);

        $this->dropDatabase($databaseManager->database('default'));
        Facade::reset();
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_finds_one_entity(): void
    {
        $user = User::findOne(['id' => 1]);
        $this::assertNotNull($user);
        $this::assertSame('Antony', $user->name);

        $user = User::findOne(['name' => 'John']);
        $this::assertNotNull($user);
        $this::assertSame(2, $user->id);
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_finds_all_entities(): void
    {
        $users = User::findAll();
        $this::assertCount(2, $users);
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_finds_entity_by_primary_key(): void
    {
        $user = User::find(1);
        $this::assertNotNull($user);
        $this::assertSame('Antony', $user->name);

        $user = User::find(2);
        $this::assertNotNull($user);
        $this::assertSame('John', $user->name);
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_uses_query_to_select_entity(): void
    {
        $user = User::query()->where('id', 1)->fetchOne();

        $this::assertNotNull($user);
        $this::assertSame('Antony', $user->name);
    }

    /**
     * @test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    #[Test]
    public function it_saves_entity(): void
    {
        $user = new User('Alex');

        $this::assertTrue($user->save()->isSuccess());
        $this::assertCount(3, User::findAll());

        $result = $this->selectEntity(User::class, cleanHeap: true)->wherePK($user->id)->fetchOne();

        $this::assertSame($result->name, $user->name);
    }
}
