<?php

declare(strict_types=1);

namespace Cycle\Tests;

use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use Cycle\Database\Driver\HandlerInterface;
use Cycle\Database\Table;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Throwable;

class DatabaseTestCase extends TestCase
{
    protected DatabaseInterface $database;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->database = $this->getContainer()->get(DatabaseInterface::class);

        /** @var Table $userTable */
        $userTable = $this->database->table('user');
        $user = $userTable->getSchema();
        $user->bigInteger('id')->primary();
        $user->string('name');
        $user->save();

        /** @var Table $identityTable */
        $identityTable = $this->database->table('identity');
        $identity = $identityTable->getSchema();
        $identity->bigPrimary('id');
        $identity->datetime('created_at');
        $identity->save();

        $identityTable->insertMultiple(['id', 'created_at'], [
            [1, '12:34:56 12-11-2020'],
            [2, '15:34:56 01-06-2021'],
        ]);
        $userTable->insertMultiple(['id', 'name'], [
            [1, 'Antony'],
            [2, 'John'],
        ]);
    }

    /**
     * @throws Throwable
     */
    protected function selectEntity(string $role, bool $cleanHeap = false): Select
    {
        $orm = $this->getContainer()->get(ORMInterface::class);

        if ($cleanHeap) {
            $orm->getHeap()->clean();
        }

        return new Select($orm, $role);
    }

    protected function dropDatabase(?Database $database = null): void
    {
        if (null === $database) {
            return;
        }

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
