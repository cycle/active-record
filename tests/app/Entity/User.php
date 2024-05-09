<?php

declare(strict_types=1);

namespace Cycle\App\Entity;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\App\Query\UserCollectionQuery;
use Cycle\App\Query\UserQuery;

#[Entity(table: 'user')]
class User extends ActiveRecord
{
    #[Column(type: 'bigInteger', primary: true, typecast: 'int')]
    public int $id;

    #[Column(type: 'string', unique: true)]
    public string $name;

    #[BelongsTo(target: Identity::class, innerKey: 'id', outerKey: 'id', cascade: true, load: 'eager')]
    public Identity $identity;

    /**
     * @return UserQuery<static>
     */
    public static function query(): UserQuery
    {
        return new UserQuery(static::class);
    }

    public static function queryWithCollection(): UserCollectionQuery
    {
        return new UserCollectionQuery(static::class);
    }

    public function __construct(string $name, ?Identity $identity = null)
    {
        $this->name = $name;
        $this->identity = $identity ?? new Identity();
    }

    public function getIdentity()
    {
        return self::query()->where('id', $this->id)->fetchOne();
    }
}
