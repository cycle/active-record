<?php

declare(strict_types=1);

namespace Cycle\App\Query;

use Cycle\ActiveRecord\Query\ActiveQuery;

class UserQueryBuilder extends ActiveQuery
{
    public function active($state = true): UserQueryBuilder
    {
        return $this->where(['active' => $state]);
    }

    public function orderByCreatedAt($direction = 'ASC'): UserQueryBuilder
    {
        return $this->orderBy(['created_at' => $direction]);
    }
}
