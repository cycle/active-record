<?php

declare(strict_types=1);

namespace Cycle\Tests;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\Schema\AbstractTable;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Throwable;

use function assert;

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

        $user = $this->database->table('user')->getSchema();
        assert($user instanceof AbstractTable);
        $user->bigInteger('id')->primary(true);
        $user->string('name');
        $user->save();

        $identity = $this->database->table('identity')->getSchema();
        assert($identity instanceof AbstractTable);
        $identity->bigPrimary('id');
        $identity->datetime('created_at');
        $identity->save();

        $this->database->table('identity')->insertMultiple(['id', 'created_at'], [
            [1, '12:34:56 12-11-2020'],
            [2, '15:34:56 01-06-2021'],
        ]);
        $this->database->table('user')->insertMultiple(['id', 'name'], [
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
        $cleanHeap and $orm->getHeap()->clean();

        return new Select($orm, $role);
    }
}
