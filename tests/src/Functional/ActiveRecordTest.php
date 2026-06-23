<?php

declare(strict_types=1);

namespace Cycle\Tests\Functional;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\ActiveRecord\Query\ActiveQuery;
use Cycle\App\Entity\Identity;
use Cycle\App\Entity\User;
use Cycle\Database\DatabaseInterface;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Exception\RunnerException;
use Cycle\ORM\Heap\HeapInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select\Repository;
use Cycle\Transaction\TransactionMode;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
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

    #[Test]
    public function it_saves_entity(): void
    {
        $user = new User('Alex');

        self::assertTrue($user->save());
        self::assertCount(3, User::findAll());

        $result = $this->selectEntity(User::class, cleanHeap: true)->wherePK($user->id)->fetchOne();

        self::assertSame($result->name, $user->name);
    }

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

    #[Test]
    public function it_persists_multiple_entities_in_single_grouping_actions_transaction(): void
    {
        ActiveRecord::groupActions(static function () use (&$userOne, &$userTwo): void {
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

    #[Test]
    public function it_deletes_entity(): void
    {
        $user = User::findByPK(1);
        self::assertNotNull($user);

        self::assertTrue($user->delete());
        self::assertCount(1, User::findAll());
    }

    #[Test]
    public function it_deletes_entity_or_fail_without_fail(): void
    {
        $user = User::findByPK(1);
        self::assertNotNull($user);

        $user->deleteOrFail();
        self::assertCount(1, User::findAll());
    }

    #[Test]
    public function it_deletes_multiple_entities_in_single_transaction_using_grouping_actions(): void
    {
        self::assertCount(2, User::findAll());

        /** @var User $userOne */
        $userOne = User::findByPK(1);
        /** @var User $userTwo */
        $userTwo = User::findByPK(2);

        ActiveRecord::groupActions(static function () use ($userOne, $userTwo): void {
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
    public function it_runs_grouping_actions_without_actions(): void
    {
        $result = ActiveRecord::groupActions(static function () {
            return 'foo';
        });

        self::assertSame('foo', $result);
    }

    #[Test]
    public function it_runs_grouping_actions_in_current_transaction_mode_without_opened_transaction(): void
    {
        self::expectException(RunnerException::class);

        ActiveRecord::groupActions(static function (): void {
            $user = User::findByPK(1);
            $user->delete();
        }, TransactionMode::Current);
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function it_runs_grouping_actions_in_grouping_actions(): void
    {
        ActiveRecord::groupActions(static function () {
            return ActiveRecord::groupActions(static fn() => true);
        }, TransactionMode::Current);
    }

    #[Test]
    public function it_runs_grouping_actions_in_strict_mode_outside_transaction(): void
    {
        self::expectException(RunnerException::class);

        ActiveRecord::groupActions(static function (): void {
            $userOne = new User('Foo');
            $userOne->saveOrFail();
        }, TransactionMode::Current);
    }

    #[Test]
    public function it_runs_grouping_actions_without_transaction_inside_manually_opened_transaction(): void
    {
        ActiveRecord::transact(static function () use (&$userOne, &$userTwo): void {
            ActiveRecord::groupActions(static function () use (&$userOne, &$userTwo): void {
                $userOne = new User('Foo');
                $userOne->saveOrFail();

                $userTwo = new User('Bar');
                $userTwo->saveOrFail();
            }, TransactionMode::Current);
        });

        self::assertCount(4, User::findAll());

        $savedUserOne = $this->selectEntity(User::class, cleanHeap: true)->wherePK($userOne->id)->fetchOne();
        self::assertSame($savedUserOne->name, $userOne->name);

        $savedUserTwo = $this->selectEntity(User::class, cleanHeap: true)->wherePK($userTwo->id)->fetchOne();
        self::assertSame($savedUserTwo->name, $userTwo->name);
    }

    #[Test]
    public function it_runs_transaction_with_group_actions_calling_on_entity_class(): void
    {
        User::transact(static function (DatabaseInterface $dbal) use (&$userOne, &$userTwo): void {
            User::groupActions(static function (EntityManagerInterface $em) use (&$userOne, &$userTwo): void {
                $userOne = new User('Foo');
                $em->persist($userOne);

                $userTwo = new User('Bar');
                $userTwo->saveOrFail();
            }, TransactionMode::Current);
        });

        self::assertCount(4, User::findAll());

        $savedUserOne = $this->selectEntity(User::class, cleanHeap: true)->wherePK($userOne->id)->fetchOne();
        self::assertSame($savedUserOne->name, $userOne->name);

        $savedUserTwo = $this->selectEntity(User::class, cleanHeap: true)->wherePK($userTwo->id)->fetchOne();
        self::assertSame($savedUserTwo->name, $userTwo->name);
    }

    #[Test]
    public function it_runs_transaction_with_orm_actions(): void
    {
        User::transact(function (
            DatabaseInterface $dbal,
            EntityManagerInterface $em,
        ) use (&$user1, &$user2, &$user3, &$user4): void {
            $user1 = new User('Foo');
            $user2 = new User('Bar');
            $user3 = new User('Baz');
            $user4 = new User('Qux');
            $user1->save();
            $user2->saveOrFail();
            $em->persist($user3);
            $em->persistState($user4);
        });

        self::assertCount(6, User::findAll());

        $savedUserOne = $this->selectEntity(User::class, cleanHeap: true)->wherePK($user1->id)->fetchOne();
        self::assertSame($savedUserOne->name, $user1->name);

        $savedUserTwo = $this->selectEntity(User::class, cleanHeap: true)->wherePK($user2->id)->fetchOne();
        self::assertSame($savedUserTwo->name, $user2->name);

        $savedUserThree = $this->selectEntity(User::class, cleanHeap: true)->wherePK($user3->id)->fetchOne();
        self::assertSame($savedUserThree->name, $user3->name);

        $savedUserFour = $this->selectEntity(User::class, cleanHeap: true)->wherePK($user4->id)->fetchOne();
        self::assertSame($savedUserFour->name, $user4->name);
    }

    #[Test]
    public function it_executes_orm_actions_immediately_within_transact(): void
    {
        User::transact(static function (DatabaseInterface $dbal, EntityManagerInterface $em): void {
            $countBefore = $dbal->table('user')->count();

            (new User('Zoe'))->save();
            $em->persist(new User('Max'));

            // FlushMode::OnWrite flushes each operation right away, so both rows are
            // already visible within the open transaction (before it is committed).
            self::assertSame($countBefore + 2, $dbal->table('user')->count());
        });

        self::assertCount(4, User::findAll());
    }

    #[Test]
    public function transact_method_resolves_parameters(): void
    {
        $ars = User::transact(static fn(
            SchemaInterface $schema,
            EntityManagerInterface $em,
            ORMInterface $orm,
            HeapInterface $heap,
            DatabaseInterface $dbal,
        ): array => \func_get_args());

        self::assertIsArray($ars);
    }

    #[Test]
    public function query_method_returns_ActiveQuery(): void
    {
        self::assertInstanceOf(ActiveQuery::class, Identity::query());
    }

    #[Test]
    public function get_table_name(): void
    {
        self::assertSame('user', User::tableName());
        self::assertSame('user_identity', Identity::tableName());
    }
}
