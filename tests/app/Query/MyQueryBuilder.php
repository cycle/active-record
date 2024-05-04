<?php

declare(strict_types=1);

namespace Cycle\App\Query;

use Cycle\ActiveRecord\Query\ActiveSelect;

class MyQueryBuilder extends ActiveSelect {
    public function active($state = true)
    {
        return $this->where(['active' => $state]);
    }

    public function orderByCreatedAt($direction = 'ASC')
    {
        return $this->orderBy(['created_at' => $direction]);
    }
}
