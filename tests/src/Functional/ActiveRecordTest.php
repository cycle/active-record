<?php

declare(strict_types=1);

namespace Cycle\Tests\Functional;

use Cycle\ActiveRecord\Facade;
use Cycle\App\Entity\User;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Cycle\ORM\Select\Repository;
use PHPUnit\Framework\Attributes\Test;

final class ActiveRecordTest extends DatabaseTestCase
{
    #[Test]
    public function it_finds_one_entity(): void
    {
        $user = User::findOne(['id' => 1]);
        self::assertNotNull($user);
        self::assertSame('Antony', $user->name);

        $user = User::findOne(['name' => 'John']);
        self::assertNotNull($user);
        self::assertSame(2, $user->id);
    }

    #[Test]
    public function it_finds_all_entities(): void
    {
        $users = User::findAll();
        self::assertCount(2, $users);
    }

    #[Test]
    public function it_finds_entity_by_primary_key(): void
    {
        $user = User::findByPK(1);
        self::assertNotNull($user);
        self::assertSame('Antony', $user->name);

        $user = User::findByPK(2);
        self::assertNotNull($user);
        self::assertSame('John', $user->name);
    }

    #[Test]
    public function it_uses_query_to_select_entity(): void
    {
        $user = User::query()->where('id', 1)->fetchOne();

        self::assertNotNull($user);
        self::assertSame('Antony', $user->name);
    }

    /**
     * @throws \Throwable
     */
    #[Test]
    public function it_saves_entity(): void
    {
        $user = new User('Alex');

        self::assertTrue($user->save()->isSuccess());
        self::assertCount(3, User::findAll());

        $result = $this->selectEntity(User::class, cleanHeap: true)->wherePK($user->id)->fetchOne();

        self::assertSame($result->name, $user->name);
    }

    /**
     * @throws \Throwable
     */
    #[Test]
    public function it_triggers_exception_when_tries_to_save_entity_using_save_or_fail(): void
    {
        $user = new User('John');

        $this::expectException(\Throwable::class);

        // pgsql-response: SQLSTATE[23505]: Unique violation: 7 ERROR:  duplicate key value violates unique constraint "user_index_name_663d5b6bf1e34
        // sqlite-response: SQLSTATE[23000]: Integrity constraint violation: 19 UNIQUE constraint failed: user.name
        // mysql-response: SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'John' for key 'user.user_index_name_663d5bc589edb'

        $this::expectExceptionMessage('SQLSTATE');

        $entityManager = $user->saveOrFail();

        self::assertFalse($entityManager->isSuccess());
        self::assertCount(2, User::findAll());
    }

    /**
     * @throws \Throwable
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

        self::assertCount(4, User::findAll());

        $savedUserOne = $this->selectEntity(User::class, cleanHeap: true)->wherePK($userOne->id)->fetchOne();
        self::assertSame($savedUserOne->name, $userOne->name);

        $savedUserTwo = $this->selectEntity(User::class, cleanHeap: true)->wherePK($userTwo->id)->fetchOne();
        self::assertSame($savedUserTwo->name, $userTwo->name);
    }

    /**
     * @throws \Throwable
     */
    #[Test]
    public function it_deletes_entity(): void
    {
        $user = User::findByPK(1);
        self::assertNotNull($user);

        self::assertTrue($user->delete()->isSuccess());
        self::assertCount(1, User::findAll());
    }

    /**
     * @throws \Throwable
     */
    #[Test]
    public function it_deletes_multiple_entities_using_remove_method(): void
    {
        /** @var User $userOne */
        $userOne = User::findByPK(1);
        /** @var User $userTwo */
        $userTwo = User::findByPK(2);

        $userOne->remove();
        $userTwo->remove();

        self::assertCount(2, User::findAll());

        $entityManager = Facade::getEntityManager();
        self::assertTrue($entityManager->run()->isSuccess());

        self::assertCount(0, User::findAll());
    }

    #[Test]
    public function it_gets_default_repository_of_entity(): void
    {
        $repository = User::getRepository();

        self::assertInstanceOf(Repository::class, $repository);
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
