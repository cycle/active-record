<?php

declare(strict_types=1);

namespace Cycle\Tests\Stub;

use Cycle\ActiveRecord\Facade;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\Testing\Database\DatabaseContext;
use Cycle\Testing\ORM\Schema\SchemaCompiler;
use Cycle\Tests\Stub\Entity\Identity;
use Cycle\Tests\Stub\Entity\Post;
use Cycle\Tests\Stub\Entity\User;
use Cycle\Transaction\Internal\TransactionImpl;
use Cycle\Transaction\Transaction;
use Spiral\Core\Container;

/**
 * The ORM of the acceptance suites: the entities, their seed and the {@see Facade} binding.
 *
 * @internal
 */
final class OrmEnvironment
{
    /**
     * Compile the schema of the entities and create their tables in the provider's databases.
     *
     * @return array<non-empty-string, array<int, mixed>>
     */
    public static function compileSchema(DatabaseProviderInterface $dbal): array
    {
        return SchemaCompiler::compile($dbal, [Identity::class, User::class, Post::class]);
    }

    /**
     * Create the tables and the users `Antony` (1) and `John` (2), once per suite.
     */
    public static function prepare(DatabaseContext $context): void
    {
        $dbal = $context->dbal();
        self::compileSchema($dbal);

        # `User` joins onto `Identity`: the identity rows come first and lend their ids.
        $dbal->database('default')->table('user_identity')->insertMultiple(['created_at'], [
            ['2020-11-12 12:34:56'],
            ['2021-01-06 15:34:56'],
        ]);
        $dbal->database('default')->table('user')->insertMultiple(['id', 'name'], [
            [1, 'Antony'],
            [2, 'John'],
        ]);
    }

    /**
     * An ORM that runs entity behaviours such as {@see \Cycle\ORM\Entity\Behavior\CreatedAt}.
     */
    public static function buildOrm(DatabaseProviderInterface $dbal, SchemaInterface $schema): ORMInterface
    {
        return new ORM(
            factory: new Factory($dbal),
            schema: $schema,
            commandGenerator: new EventDrivenCommandGenerator($schema, new Container()),
        );
    }

    /**
     * Point the {@see Facade} at the test's ORM.
     *
     * @return \Closure(): void Resets the facade.
     */
    public static function bindFacade(ORMInterface $orm, DatabaseContext $context): \Closure
    {
        $container = new Container();
        $container->bindSingleton(ORMInterface::class, $orm);
        $container->bindSingleton(DatabaseManager::class, $context->dbal());
        $container->bindSingleton(Transaction::class, new TransactionImpl($orm, $context->dbal()));
        Facade::setContainer($container);

        return static fn() => Facade::reset();
    }
}
