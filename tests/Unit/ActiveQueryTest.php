<?php

declare(strict_types=1);

namespace Cycle\Tests\Unit;

use Cycle\ActiveRecord\Facade;
use Cycle\ActiveRecord\Query\ActiveQuery;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\Testing\ORM\Memory;
use Cycle\Tests\Unit\Stub\Container\ConfigurableContainer;
use Testo\Assert;
use Testo\Lifecycle\AfterTest;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

#[Test]
final class ActiveQueryTest
{
    #[BeforeTest]
    #[AfterTest]
    public function resetFacade(): void
    {
        Facade::reset();
    }

    public function selectsTheRoleThroughTheFacadeOrm(): void
    {
        $orm = Memory::orm([
            'note' => [
                SchemaInterface::ENTITY => \stdClass::class,
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'notes',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::COLUMNS => ['id', 'text'],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]);
        Facade::setContainer(ConfigurableContainer::returning(ORMInterface::class, $orm));

        $query = new ActiveQuery('note');

        Assert::same($query->getRole(), 'note');
        Assert::string($query->sqlStatement())->contains('FROM "notes"');
    }
}
