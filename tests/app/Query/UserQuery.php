<?php

declare(strict_types=1);

namespace Cycle\App\Query;

use Cycle\ActiveRecord\Query\ActiveQuery;
use Cycle\App\Entity\User;

/**
 * @extends ActiveQuery<User>
 */
class UserQuery extends ActiveQuery
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function active(bool $state = true): UserQuery
    {
        return $this->where(['active' => $state]);
    }

    public function orderByCreatedAt(string $direction = 'ASC'): UserQuery
    {
        return $this->orderBy(['created_at' => $direction]);
    }
}
