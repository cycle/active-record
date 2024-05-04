<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Query;

use Cycle\ActiveRecord\Facade;
use Cycle\ORM\Select;

/**
 * @template TEntity of object
 *
 * @extends Select<TEntity>
 */
class ActiveQuery extends Select
{
    /**
     * @param class-string<TEntity> $class
     */
    final public function __construct(string $class)
    {
        parent::__construct(Facade::getOrm(), $class);
    }
}
