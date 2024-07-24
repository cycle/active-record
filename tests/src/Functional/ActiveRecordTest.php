<?php

declare(strict_types=1);

namespace Cycle\Tests\Functional;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\ActiveRecord\Exception\Transaction\TransactionException;
use Cycle\ActiveRecord\TransactionMode;
use Cycle\App\Entity\User;
use Cycle\ORM\Exception\RunnerException;
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

    #[Test]
    public function it_creates_entity_instance_using_make(): void
    {
        $user = User::make(['name' => 'Alex']);

        self::assertInstanceOf(User::class, $user);
        self::assertNotSame(User::class, $user::class, 'An Entity Proxy is created');
        self::assertSame('Alex', $user->name);
    }

    /**
     * @throws \Throwable
     */
    #[Test]
    public function it_saves_entity(): void
    {
        $user = new User('Alex');

        self::assertTrue($user->save());
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

        self::expectException(\Throwable::class);

        // pgsql-response: SQLSTATE[23505]: Unique violation: 7 ERROR:  duplicate key value violates unique constraint "user_index_name_663d5b6bf1e34
        // sqlite-response: SQLSTATE[23000]: Integrity constraint violation: 19 UNIQUE constraint failed: user.name
        // mysql-response: SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'John' for key 'user.user_index_name_663d5bc589edb'

        self::expectExceptionMessage('SQLSTATE');

        $entityManager = $user->saveOrFail();

        self::assertFalse($entityManager->isSuccess());
        self::assertCount(2, User::findAll());
    }

    /**
     * @throws \Throwable
     */
    #[Test]
    public function it_persists_multiple_entities_in_single_transaction(): void
    {
        ActiveRecord::transact(static function () use (&$userOne, &$userTwo): void {
            $userOne = new User('Foo');
            $userOne->saveOrFail();

            $userTwo = new User('Bar');
            $userTwo->saveOrFail();
        });

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

        self::assertTrue($user->delete());
        self::assertCount(1, User::findAll());
    }

    /**
     * @throws \Throwable
     */
    #[Test]
    public function it_deletes_multiple_entities_in_single_transaction(): void
    {
        self::assertCount(2, User::findAll());

        /** @var User $userOne */
        $userOne = User::findByPK(1);
        /** @var User $userTwo */
        $userTwo = User::findByPK(2);

        ActiveRecord::transact(static function () use ($userOne, $userTwo): void {
            $userOne->delete();
            $userTwo->delete();
        });

        self::assertCount(0, User::findAll());
    }

    #[Test]
    public function it_gets_default_repository_of_entity(): void
    {
        $repository = User::getRepository();

        self::assertInstanceOf(Repository::class, $repository);
    }

    #[Test]
    public function it_runs_transaction_without_actions(): void
    {
        $result = ActiveRecord::transact(static function () {
            return 'foo';
        });

        self::assertSame('foo', $result);
    }

    #[Test]
    public function it_runs_transaction_in_current_transaction_mode_without_opened_transaction(): void
    {
        self::expectException(RunnerException::class);

        ActiveRecord::transact(static function (): void {
            $user = User::findByPK(1);
            $user->delete();
        }, TransactionMode::Current);
    }

    #[Test]
    public function it_runs_transaction_in_transaction(): void
    {
        self::expectException(TransactionException::class);

        ActiveRecord::transact(static function () {
            return ActiveRecord::transact(static fn() => true);
        }, TransactionMode::Current);
    }
}
