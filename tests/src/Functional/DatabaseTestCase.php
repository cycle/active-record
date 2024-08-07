<?php

declare(strict_types=1);

namespace Cycle\Tests\Functional;

use Cycle\ActiveRecord\Facade;
use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Driver\DriverInterface;
use Cycle\Database\Driver\HandlerInterface;
use Cycle\Database\Table;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;

class DatabaseTestCase extends TestCase
{
    use Loggable;

    protected DatabaseInterface $database;

    protected ORMInterface $orm;

    public function getDriver(): DriverInterface
    {
        return $this->database->getDriver();
    }

    /**
     * @throws \Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->database = $this->getContainer()->get(DatabaseInterface::class);
        $this->setUpLogger($this->getDriver());

        if (env('DEBUG', false) === true) {
            $this->enableProfiling();
        }

        $this->orm = $this->getContainer()->get(ORMInterface::class);

        /** @var Table $userTable */
        $userTable = $this->database->table('user');
        /** @var Table $identityTable */
        $identityTable = $this->database->table('user_identity');

        $identityTable->insertMultiple(['created_at'], [
            ['2020-11-12 12:34:56'],
            ['2021-01-06 15:34:56'],
        ]);
        $userTable->insertMultiple(['id', 'name'], [
            [1, 'Antony'],
            [2, 'John'],
        ]);
    }

    /**
     * @throws \Throwable
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $databaseManager = $this->getContainer()->get(DatabaseManager::class);
        /** @var Database $database */
        $database = $databaseManager->database('default');

        $this->dropDatabase($database);
        Facade::reset();
    }

    /**
     * @throws \Throwable
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
        if ($database === null) {
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
