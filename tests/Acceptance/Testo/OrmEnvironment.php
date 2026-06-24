<?php

declare(strict_types=1);

namespace Cycle\Tests\Acceptance\Testo;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\Tests\Stub\Entity\Identity;
use Cycle\Tests\Stub\Entity\Post;
use Cycle\Tests\Stub\Entity\User;
use Cycle\Database\Config\DriverConfig;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Driver\HandlerInterface;
use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Parser\Typecast;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateModifiers;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderModifiers;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Psr\Container\ContainerInterface;

/**
 * Stateless helpers for setting up the Cycle ORM test environment.
 *
 * Building an ORM is cheap once the schema is compiled, so the heavy {@see self::compileSchema()} is
 * done once (see {@see DatabasePlugin}) and the resulting {@see SchemaInterface} is reused to build a
 * fresh ORM per case via {@see self::buildOrm()}.
 *
 * @internal
 */
final class OrmEnvironment
{
    /**
     * Compile the ORM schema from the real application entities ({@see User}, {@see Identity},
     * {@see Post}) through the same Annotated + Schema generator pipeline the framework uses —
     * including entity behaviours such as {@see \Cycle\ORM\Entity\Behavior\CreatedAt}.
     *
     * The pipeline also synchronises the physical tables on the given manager, so compiling against a
     * connection both yields the (driver-agnostic) schema array and creates that connection's tables.
     *
     * @return array<non-empty-string, array<int, mixed>>
     */
    public static function compileSchema(DatabaseManager $dbal): array
    {
        $locator = new EntityClassLocator(Identity::class, User::class, Post::class);

        return (new Compiler())->compile(
            registry: new Registry($dbal),
            generators: [
                new ResetTables(),
                new Embeddings(new TokenizerEmbeddingLocator($locator)),
                new Entities(new TokenizerEntityLocator($locator)),
                new TableInheritance(),
                new MergeColumns(),
                new GenerateRelations(),
                new GenerateModifiers(),
                new ValidateEntities(),
                new RenderTables(),
                new RenderRelations(),
                new RenderModifiers(),
                new MergeIndexes(),
                new SyncTables(),
                new GenerateTypecast(),
            ],
            defaults: [
                SchemaInterface::TYPECAST_HANDLER => [Typecast::class],
            ],
        );
    }

    /**
     * Build an ORM for the given connection and pre-compiled schema.
     *
     * When a container is supplied, entity behaviours are wired through an
     * {@see EventDrivenCommandGenerator} that resolves its listeners from it; otherwise a plain ORM is
     * returned (enough for tests that only need an {@see ORMInterface} instance).
     */
    public static function buildOrm(
        DatabaseManager $dbal,
        SchemaInterface $schema,
        ?ContainerInterface $behaviors = null,
    ): ORMInterface {
        return new ORM(
            factory: new Factory($dbal),
            schema: $schema,
            commandGenerator: $behaviors === null ? null : new EventDrivenCommandGenerator($schema, $behaviors),
        );
    }

    /**
     * Convenience factory for a standalone ORM whose `default` connection uses the given driver (the
     * `secondary` connection is always in-memory SQLite). Used by tests that need an ORM without the
     * plugin.
     */
    public static function forDriver(DriverConfig $defaultDriver): ORMInterface
    {
        $dbal = ConnectionPool::createManager($defaultDriver);

        return self::buildOrm($dbal, new Schema(self::compileSchema($dbal)));
    }

    /**
     * Seed the baseline dataset: two users (`Antony`, `John`) backed by their identity rows.
     *
     * `User` joins onto `Identity`, so the identity rows must exist first; their auto-incremented ids
     * (1 and 2) are then reused as the explicit user ids. Seeded once per driver and kept committed —
     * each test runs inside a transaction that is rolled back, so the seed is never disturbed.
     */
    public static function seed(DatabaseManager $dbal): void
    {
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
     * Drop every table from both connections of the given manager. Safe to call when the tables do
     * not exist yet, so it doubles as a defensive reset before building the schema for a driver.
     */
    public static function purge(DatabaseManager $dbal): void
    {
        foreach (['default', 'secondary'] as $name) {
            self::dropDatabase($dbal->database($name));
        }
    }

    private static function dropDatabase(Database $database): void
    {
        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();

            foreach ($schema->getForeignKeys() as $foreign) {
                $schema->dropForeignKey($foreign->getColumns());
            }

            $schema->save(HandlerInterface::DROP_FOREIGN_KEYS);
        }

        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();
            $schema->declareDropped();
            $schema->save();
        }
    }
}
