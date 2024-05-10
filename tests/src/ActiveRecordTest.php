<?php

declare(strict_types=1);

namespace Cycle\Tests;

use Cycle\ActiveRecord\Facade;
use Cycle\App\Entity\User;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Cycle\ORM\Select\Repository;
use PHPUnit\Framework\Attributes\Test;
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
        /** @var Database $database */
        $database = $databaseManager->database('default');

        $this->dropDatabase($database);
        Facade::reset();
    }

    /**
     * @test
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
     */
    #[Test]
    public function it_finds_all_entities(): void
    {
        $users = User::findAll();
        $this::assertCount(2, $users);
    }

    /**
     * @test
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

    /**
     * @test
     *
     * @throws Throwable
     */
    #[Test]
    public function it_triggers_exception_when_tries_to_save_entity_using_save_or_fail(): void
    {
        $user = new User('John');

        $this::expectException(Throwable::class);

        // pgsql-response: SQLSTATE[23505]: Unique violation: 7 ERROR:  duplicate key value violates unique constraint "user_index_name_663d5b6bf1e34
        // sqlite-response: SQLSTATE[23000]: Integrity constraint violation: 19 UNIQUE constraint failed: user.name
        // mysql-response: SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'John' for key 'user.user_index_name_663d5bc589edb'

        $this::expectExceptionMessage('SQLSTATE');

        $entityManager = $user->saveOrFail();

        $this::assertFalse($entityManager->isSuccess());
        $this::assertCount(2, User::findAll());
    }

    /**
     * @test
     *
     * @throws Throwable
     */
    #[Test]
    public function it_persists_multiple_entities(): void
    {
        $userOne = new User('Foo');
        $userOne->persist();

        $userTwo = new User('Bar');
        $userTwo->persist();

        $entityManager = Facade::getEntityManager();
        $entityManager->run();

        $this::assertCount(4, User::findAll());

        $savedUserOne = $this->selectEntity(User::class, cleanHeap: true)->wherePK($userOne->id)->fetchOne();
        $this::assertSame($savedUserOne->name, $userOne->name);

        $savedUserTwo = $this->selectEntity(User::class, cleanHeap: true)->wherePK($userTwo->id)->fetchOne();
        $this::assertSame($savedUserTwo->name, $userTwo->name);
    }

    /**
     * @test
     *
     * @throws Throwable
     */
    #[Test]
    public function it_deletes_entity(): void
    {
        $user = User::find(1);
        $this::assertNotNull($user);

        $this::assertTrue($user->delete()->isSuccess());
        $this::assertCount(1, User::findAll());
    }

    /**
     * @test
     *
     * @throws Throwable
     */
    #[Test]
    public function it_deletes_multiple_entities_using_remove_method(): void
    {
        /** @var User $userOne */
        $userOne = User::find(1);
        /** @var User $userTwo */
        $userTwo = User::find(2);

        $userOne->remove();
        $userTwo->remove();

        $this::assertCount(2, User::findAll());

        $entityManager = Facade::getEntityManager();
        $this::assertTrue($entityManager->run()->isSuccess());

        $this::assertCount(0, User::findAll());
    }

    /**
     * @test
     */
    #[Test]
    public function it_gets_default_repository_of_entity(): void
    {
        $repository = User::getRepository();

        $this::assertInstanceOf(Repository::class, $repository);
    }
}
