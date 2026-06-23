<?php

declare(strict_types=1);

namespace Cycle\Tests\Functional;

use Cycle\App\Entity\Post;
use Cycle\App\Entity\User;
use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\Transaction\Exception\TransactionException;
use PHPUnit\Framework\Attributes\Test;

final class MultiDatabaseTest extends DatabaseTestCase
{
    #[Test]
    public function it_persists_an_entity_into_its_own_database(): void
    {
        $post = new Post('Hello');

        self::assertTrue($post->save());
        self::assertGreaterThan(0, $post->id);

        // The row physically lives in the `secondary` database.
        $secondary = $this->getContainer()->get(DatabaseManager::class)->database('secondary');
        self::assertSame(1, $secondary->table('post')->count());

        // ...and not in the default one.
        self::assertFalse($this->database->hasTable('post'));
    }

    #[Test]
    public function it_runs_independent_transactions_per_database(): void
    {
        User::transact(static function (EntityManagerInterface $em): void {
            $em->persist(new User('Alice'));
        });

        Post::transact(static function (EntityManagerInterface $em): void {
            $em->persist(new Post('First'));
        });

        // 2 seeded users + 1 created here.
        self::assertCount(3, User::findAll());
        self::assertCount(1, Post::findAll());
    }

    #[Test]
    public function it_rejects_persisting_an_entity_from_a_different_database_within_a_transaction(): void
    {
        self::expectException(TransactionException::class);

        // The transaction is opened on the default database (via User),
        // so a Post (secondary database) must be rejected by the EM guard.
        User::transact(static function (EntityManagerInterface $em): void {
            $em->persist(new Post('Should not be allowed'));
        });
    }

    #[Test]
    public function it_rolls_back_a_failed_transaction_without_touching_the_other_database(): void
    {
        try {
            Post::transact(static function (EntityManagerInterface $em): void {
                $em->persist(new Post('Doomed'));
                throw new \RuntimeException('boom');
            });
        } catch (\RuntimeException) {
            // expected
        }

        self::assertCount(0, Post::findAll());
        // The default database is untouched and still holds the seeded rows.
        self::assertCount(2, User::findAll());
    }

    #[Test]
    public function default_and_secondary_use_distinct_drivers(): void
    {
        $dbm = $this->getContainer()->get(DatabaseManager::class);

        $defaultDriver = $dbm->database('default')->getDriver(DatabaseInterface::WRITE)->getName();
        $secondaryDriver = $dbm->database('secondary')->getDriver(DatabaseInterface::WRITE)->getName();

        self::assertNotSame($defaultDriver, $secondaryDriver);
        self::assertSame('secondary', $secondaryDriver);
    }
}
