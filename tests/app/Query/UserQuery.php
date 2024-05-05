<?php

declare(strict_types=1);

namespace Cycle\App\Query;

use Cycle\ActiveRecord\Query\ActiveQuery;

/**
 * @template-covariant TEntity of object
 *
 * @extends ActiveQuery<TEntity>
 */
class UserQuery extends ActiveQuery
{
    public function active(bool $state = true): UserQuery
    {
        return $this->where(['active' => $state]);
    }

    public function orderByCreatedAt(string $direction = 'ASC'): UserQuery
    {
        return $this->orderBy(['created_at' => $direction]);
    }
}
