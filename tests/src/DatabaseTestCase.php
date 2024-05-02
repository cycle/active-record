<?php

declare(strict_types=1);

namespace Cycle\Tests;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\Table;
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
}
