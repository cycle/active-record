<?php

declare(strict_types=1);

namespace Cycle\App\Query;

use Cycle\ActiveRecord\Attribute\Collection;
use Cycle\ActiveRecord\Query\ActiveQuery;
use Cycle\ORM\Collection\IlluminateCollectionFactory;

/**
 * @template-covariant TEntity of object
 *
 * @extends ActiveQuery<TEntity>
 */
#[Collection(name: IlluminateCollectionFactory::class)]
class UserCollectionQuery extends ActiveQuery
{
    public function active(bool $state = true): UserCollectionQuery
    {
        return $this->where(['active' => $state]);
    }
}
