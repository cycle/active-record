<?php

declare(strict_types=1);

namespace Cycle\Tests;

use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use Cycle\Database\Driver\DriverInterface;
use Cycle\Database\Driver\HandlerInterface;
use Cycle\Database\Table;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Throwable;

class DatabaseTestCase extends TestCase
{
    use Loggable;

    protected DatabaseInterface $database;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->database = $this->getContainer()->get(DatabaseInterface::class);

        $this->setUpLogger($this->getDriver());
        $this->enableProfiling();

        /** @var Table $userTable */
        $userTable = $this->database->table('user');
        $user = $userTable->getSchema();
        $user->bigPrimary('id');
        $user->string('name');
        $user->index(['name'])->unique(true);
        $user->save();

        /** @var Table $identityTable */
        $identityTable = $this->database->table('user_identity');
        $identity = $identityTable->getSchema();
        $identity->bigPrimary('id');
        $identity->datetime('created_at');
        $identity->save();

        $identityTable->insertMultiple(['created_at'], [
            ['2020-11-12 12:34:56'],
            ['2021-01-06 15:34:56'],
        ]);
        $userTable->insertMultiple(['name'], [
            ['Antony'],
            ['John'],
        ]);
    }

    public function getDriver(): DriverInterface
    {
        return $this->database->getDriver();
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
