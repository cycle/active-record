<?php

declare(strict_types=1);

namespace Cycle\App\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\JoinedTable;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\App\Query\UserQuery;

#[Entity(table: 'user')]
#[JoinedTable]
#[Index(columns: ['name'], unique: true)]
class User extends Identity
{
    #[Column(type: 'string')]
    public string $name;

    /**
     * @return UserQuery<static>
     */
    public static function query(): UserQuery
    {
        return new UserQuery(static::class);
    }

    public function __construct(string $name)
    {
        parent::__construct();
        $this->name = $name;
    }

    public function getIdentity()
    {
        return self::query()->where('id', $this->id)->fetchOne();
    }
}
