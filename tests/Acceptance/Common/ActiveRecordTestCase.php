<?php

declare(strict_types=1);

namespace Cycle\Tests\Acceptance\Common;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\ActiveRecord\Facade;
use Cycle\ActiveRecord\Internal\TransactionFacade;
use Cycle\ActiveRecord\Query\ActiveQuery;
use Cycle\ActiveRecord\Repository\ActiveRepository;
use Cycle\Tests\Stub\Entity\Identity;
use Cycle\Tests\Stub\Entity\Post;
use Cycle\Tests\Stub\Entity\User;
use Cycle\Tests\Stub\Repository\RepositoryWithActiveQuery;
use Cycle\Database\DatabaseInterface;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Exception\RunnerException;
use Cycle\ORM\Heap\HeapInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select\Repository;
use Cycle\Tests\Acceptance\Testo\WithoutTransaction;
use Cycle\Transaction\Exception\TransactionException;
use Cycle\Transaction\TransactionMode;
use Testo\Assert;
use Testo\Codecov\Covers;
use Testo\Expect;
use Testo\Filter\Group;

/**
 * Acceptance scenarios for the ActiveRecord pattern executed against a real database.
 *
 * Abstract: discovered and run only through the concrete per-driver subclasses under
 * `tests/Acceptance/Driver`. Each subclass selects its driver with a `#[Group('driver-*')]`; the
 * {@see \Cycle\Tests\Acceptance\Testo\DatabaseInterceptor} builds the ORM, seeds the data and binds it
 * to the {@see Facade} before every test.
 */
#[Group('driver')]
#[Covers(ActiveRecord::class)]
#[Covers(ActiveRepository::class)]
#[Covers(ActiveQuery::class)]
#[Covers(Facade::class)]
#[Covers(TransactionFacade::class)]
abstract class ActiveRecordTestCase extends BaseTestCase
{
    // region Finding entities

    public function findsOneEntityByScope(): void
    {
        $user = User::findOne(['id' => 1]);
        Assert::instanceOf($user, User::class);
        Assert::same($user->name, 'Antony');

        $byName = User::findOne(['name' => 'John']);
        Assert::instanceOf($byName, User::class);
        Assert::same($byName->id, 2);
    }

    public function findsAllEntities(): void
    {
        Assert::count(User::findAll(), 2);
    }

    public function findsEntityByPrimaryKey(): void
    {
        $first = User::findByPK(1);
        Assert::instanceOf($first, User::class);
        Assert::same($first->name, 'Antony');

        $second = User::findByPK(2);
        Assert::instanceOf($second, User::class);
        Assert::same($second->name, 'John');
    }

    public function selectsEntityViaQuery(): void
    {
        $user = User::query()->where('id', 1)->fetchOne();

        Assert::instanceOf($user, User::class);
        Assert::same($user->name, 'Antony');
    }

    public function makeCreatesProxyInstance(): void
    {
        $user = User::make(['name' => 'Alex']);

        Assert::instanceOf($user, User::class);
        Assert::notSame($user::class, User::class);
        Assert::same($user->name, 'Alex');
    }

    // endregion

    // region Persisting and deleting

    public function savesEntity(): void
    {
        $user = new User('Alex');

        Assert::true($user->save());
        Assert::count(User::findAll(), 3);

        $stored = $this->selectEntity(User::class, cleanHeap: true)->wherePK($user->id)->fetchOne();
        Assert::same($stored->name, $user->name);
    }

    public function savesEntityWithinTransaction(): void
    {
        ActiveRecord::transact(static function (EntityManagerInterface $em) use (&$user): void {
            $user = new User('Alex');
            Assert::true($user->save());
        });

        Assert::count(User::findAll(), 3);
        $stored = $this->selectEntity(User::class, cleanHeap: true)->wherePK($user->id)->fetchOne();
        Assert::same($stored->name, $user->name);
    }

    public function saveOrFailThrowsOnUniqueViolation(): never
    {
        // `name` carries a unique index; persisting a duplicate must surface the driver error.
        Expect::exception(\Throwable::class)->withMessageContaining('SQLSTATE');

        (new User('John'))->saveOrFail();
    }

    public function deletesEntity(): void
    {
        $user = User::findByPK(1);
        Assert::instanceOf($user, User::class);

        Assert::true($user->delete());
        Assert::count(User::findAll(), 1);
    }

    public function deleteOrFailRemovesEntity(): void
    {
        $user = User::findByPK(1);
        Assert::instanceOf($user, User::class);

        $user->deleteOrFail();
        Assert::count(User::findAll(), 1);
    }

    // endregion

    // region Grouped actions and transactions

    public function groupActionsPersistsMultipleEntities(): void
    {
        ActiveRecord::groupActions(static function () use (&$userOne, &$userTwo): void {
            ($userOne = new User('Foo'))->saveOrFail();
            ($userTwo = new User('Bar'))->saveOrFail();
        });

        Assert::count(User::findAll(), 4);

        $storedOne = $this->selectEntity(User::class, cleanHeap: true)->wherePK($userOne->id)->fetchOne();
        Assert::same($storedOne->name, $userOne->name);

        $storedTwo = $this->selectEntity(User::class, cleanHeap: true)->wherePK($userTwo->id)->fetchOne();
        Assert::same($storedTwo->name, $userTwo->name);
    }

    public function groupActionsReturnsValueWithoutActions(): void
    {
        $result = ActiveRecord::groupActions(static fn(): string => 'foo');

        Assert::same($result, 'foo');
    }

    public function groupActionsNestedInCurrentModeRunsInner(): void
    {
        $result = ActiveRecord::groupActions(
            static fn() => ActiveRecord::groupActions(static fn(): bool => true),
            TransactionMode::Current,
        );

        Assert::true($result);
    }

    #[WithoutTransaction]
    public function groupActionsInCurrentModeWithoutTransactionThrows(): never
    {
        Expect::exception(RunnerException::class);

        ActiveRecord::groupActions(static function (): void {
            User::findByPK(1)->delete();
        }, TransactionMode::Current);
    }

    #[WithoutTransaction]
    public function groupActionsInStrictModeOutsideTransactionThrows(): never
    {
        Expect::exception(RunnerException::class);

        ActiveRecord::groupActions(static function (): void {
            (new User('Foo'))->saveOrFail();
        }, TransactionMode::Current);
    }

    #[WithoutTransaction]
    public function groupActionsInIgnoreModeOutsideTransactionSucceeds(): void
    {
        // When outside a transaction, TransactionMode::Ignore (with strict: false) should work fine.
        // The runner will ignore the lack of a transaction.
        // If mutated to strict: true, this would throw RunnerException because there's no active transaction.
        $result = ActiveRecord::groupActions(static function () use (&$created, &$deleted): string {
            // Create and immediately delete an entity to generate commands without leaving side effects
            $user = new User('TestUser');
            $created = $user->save(); // Must succeed without transaction
            $deleted = $user->delete(); // Must also succeed
            return 'success';
        }, TransactionMode::Ignore);
        Assert::same($result, 'success');
        Assert::true($created);
        Assert::true($deleted);
    }

    public function deletesMultipleEntitiesInGroupActions(): void
    {
        Assert::count(User::findAll(), 2);

        $userOne = User::findByPK(1);
        $userTwo = User::findByPK(2);

        ActiveRecord::groupActions(static function () use ($userOne, $userTwo): void {
            Assert::true($userOne->delete());
            Assert::true($userTwo->delete());
        });

        Assert::count(User::findAll(), 0);
    }

    public function transactRunsGroupActionsInsideManualTransaction(): void
    {
        ActiveRecord::transact(static function () use (&$userOne, &$userTwo): void {
            ActiveRecord::groupActions(static function () use (&$userOne, &$userTwo): void {
                ($userOne = new User('Foo'))->saveOrFail();
                ($userTwo = new User('Bar'))->saveOrFail();
            }, TransactionMode::Current);
        });

        Assert::count(User::findAll(), 4);

        $storedOne = $this->selectEntity(User::class, cleanHeap: true)->wherePK($userOne->id)->fetchOne();
        Assert::same($storedOne->name, $userOne->name);
    }

    public function transactOnEntityClassRunsGroupActions(): void
    {
        User::transact(static function (DatabaseInterface $dbal) use (&$userOne, &$userTwo): void {
            User::groupActions(static function (EntityManagerInterface $em) use (&$userOne, &$userTwo): void {
                $em->persist($userOne = new User('Foo'));

                ($userTwo = new User('Bar'))->saveOrFail();
            }, TransactionMode::Current);
        });

        Assert::count(User::findAll(), 4);

        $storedOne = $this->selectEntity(User::class, cleanHeap: true)->wherePK($userOne->id)->fetchOne();
        Assert::same($storedOne->name, $userOne->name);

        $storedTwo = $this->selectEntity(User::class, cleanHeap: true)->wherePK($userTwo->id)->fetchOne();
        Assert::same($storedTwo->name, $userTwo->name);
    }

    public function groupActionsRestoresEntityManagerAfterCompletion(): void
    {
        $emBefore = TransactionFacade::getEntityManager();

        ActiveRecord::groupActions(static function (): void {
            (new User('Foo'))->saveOrFail();
        });

        $emAfter = TransactionFacade::getEntityManager();

        Assert::same($emBefore, $emAfter);
    }

    public function groupActionsRestoresEntityManagerAfterException(): void
    {
        $emBefore = TransactionFacade::getEntityManager();

        try {
            ActiveRecord::groupActions(static function (): never {
                throw new \RuntimeException('test error');
            });
        } catch (\RuntimeException) {
            // Expected
        }

        $emAfter = TransactionFacade::getEntityManager();

        Assert::same($emBefore, $emAfter);
    }

    public function nestedGroupActionsRestoreContextProperly(): void
    {
        $emBefore = TransactionFacade::getEntityManager();

        ActiveRecord::groupActions(static function () use ($emBefore): void {
            $emInOuter = TransactionFacade::getEntityManager();
            Assert::notSame($emInOuter, $emBefore);

            ActiveRecord::groupActions(static function () use ($emInOuter): void {
                $emInInner = TransactionFacade::getEntityManager();
                Assert::notSame($emInInner, $emInOuter);
                (new User('Bar'))->saveOrFail();
            }, TransactionMode::Current);

            $emAfterInner = TransactionFacade::getEntityManager();
            Assert::same($emAfterInner, $emInOuter);
        });

        $emAfter = TransactionFacade::getEntityManager();
        Assert::same($emAfter, $emBefore);
    }

    public function transactExecutesOrmActions(): void
    {
        User::transact(function (
            DatabaseInterface $dbal,
            EntityManagerInterface $em,
        ) use (&$user1, &$user2, &$user3, &$user4): void {
            ($user1 = new User('Foo'))->save();
            ($user2 = new User('Bar'))->saveOrFail();
            $em->persist($user3 = new User('Baz'));
            $em->persistState($user4 = new User('Qux'));
        });

        Assert::count(User::findAll(), 6);

        foreach ([$user1, $user2, $user3, $user4] as $created) {
            $stored = $this->selectEntity(User::class, cleanHeap: true)->wherePK($created->id)->fetchOne();
            Assert::same($stored->name, $created->name);
        }
    }

    public function transactExecutesOrmActionsImmediately(): void
    {
        User::transact(static function (DatabaseInterface $dbal, EntityManagerInterface $em): void {
            $before = $dbal->table('user')->count();

            (new User('Zoe'))->save();
            $em->persist(new User('Max'));

            // FlushMode::OnWrite flushes each operation right away, so both rows are already
            // visible within the open transaction (before it is committed).
            Assert::same($dbal->table('user')->count(), $before + 2);
        });

        Assert::count(User::findAll(), 4);
    }

    public function transactResolvesCallbackParameters(): void
    {
        $args = User::transact(static fn(
            SchemaInterface $schema,
            EntityManagerInterface $em,
            ORMInterface $orm,
            HeapInterface $heap,
            DatabaseInterface $dbal,
        ): array => \func_get_args());

        Assert::array($args)->hasCount(5);
    }

    public function transactRestoresEntityManagerAfterException(): void
    {
        $emBefore = TransactionFacade::getEntityManager();

        try {
            User::transact(static function (): never {
                throw new \RuntimeException('test error');
            });
        } catch (\RuntimeException) {
            // Expected
        }

        $emAfter = TransactionFacade::getEntityManager();

        Assert::same($emBefore, $emAfter);
    }

    // endregion

    // region Multiple databases

    public function persistsEntityIntoItsOwnDatabase(): void
    {
        $post = new Post('Hello');

        Assert::true($post->save());
        Assert::int($post->id)->greaterThan(0);

        // The row physically lives in the `secondary` database...
        Assert::same($this->database('secondary')->table('post')->count(), 1);
        // ...and not in the default one.
        Assert::false($this->database()->hasTable('post'));
    }

    public function runsIndependentTransactionsPerDatabase(): void
    {
        User::transact(static fn(EntityManagerInterface $em) => $em->persist(new User('Alice')));
        Post::transact(static fn(EntityManagerInterface $em) => $em->persist(new Post('First')));

        Assert::count(User::findAll(), 3);
        Assert::count(Post::findAll(), 1);
    }

    public function rejectsCrossDatabaseEntityWithinTransaction(): never
    {
        Expect::exception(TransactionException::class);

        // The transaction is opened on the default database (via User), so a Post (secondary
        // database) must be rejected by the Entity Manager guard.
        User::transact(static fn(EntityManagerInterface $em) => $em->persist(new Post('Should not be allowed')));
    }

    public function rollsBackFailedTransactionWithoutTouchingOtherDatabase(): void
    {
        try {
            Post::transact(static function (EntityManagerInterface $em): void {
                $em->persist(new Post('Doomed'));
                throw new \RuntimeException('boom');
            });
            Assert::fail('Expected the callback exception to propagate.');
        } catch (\RuntimeException $e) {
            Assert::same($e->getMessage(), 'boom');
        }

        Assert::count(Post::findAll(), 0);
        Assert::count(User::findAll(), 2);
    }

    public function defaultAndSecondaryUseDistinctDrivers(): void
    {
        $defaultDriver = $this->database('default')->getDriver(DatabaseInterface::WRITE)->getName();
        $secondaryDriver = $this->database('secondary')->getDriver(DatabaseInterface::WRITE)->getName();

        Assert::notSame($defaultDriver, $secondaryDriver);
        Assert::same($secondaryDriver, 'secondary');
    }

    // endregion

    // region Repositories and queries

    public function defaultRepositoryIsReturned(): void
    {
        Assert::instanceOf(User::getRepository(), Repository::class);
    }

    public function repositoryFetchesOneEntity(): void
    {
        $user = (new ActiveRepository(User::class))->findOne(['id' => 2]);

        Assert::instanceOf($user, User::class);
        Assert::same($user->id, 2);
    }

    public function repositoryFetchesOneEntityByPrimaryKey(): void
    {
        $user = (new ActiveRepository(User::class))->findByPK(2);

        Assert::instanceOf($user, User::class);
        Assert::same($user->id, 2);
    }

    public function repositoryFetchesAllEntities(): void
    {
        $users = (new ActiveRepository(User::class))->findAll();

        Assert::count($users, 2);
        foreach ($users as $user) {
            Assert::instanceOf($user, User::class);
        }
    }

    public function customRepositoryUsesActiveQuery(): void
    {
        $user = (new RepositoryWithActiveQuery())->withNameStartLetter('J')->findOne();

        Assert::instanceOf($user, User::class);
        Assert::same($user->name[0], 'J');
    }

    public function extendedRepositoryConstructorResolvesEntity(): void
    {
        $repository = new class extends ActiveRepository {
            public function __construct()
            {
                parent::__construct(User::class);
            }
        };

        Assert::instanceOf($repository->findOne(), User::class);
    }

    public function selectMethodIsImmutable(): void
    {
        $repository = new ActiveRepository(User::class);

        Assert::notSame($repository->select(), $repository->select());
    }

    public function forUpdateMethodIsImmutable(): void
    {
        $repository = new ActiveRepository(User::class);

        Assert::notSame($repository->forUpdate(), $repository->forUpdate());
    }

    public function queryReturnsActiveQueryForRole(): void
    {
        Assert::instanceOf(Identity::query(), ActiveQuery::class);
        Assert::same(User::query()->getRole(), User::class);
    }

    public function tableNameIsResolvedFromSchema(): void
    {
        Assert::same(User::tableName(), 'user');
        Assert::same(Identity::tableName(), 'user_identity');
    }

    // endregion
}
