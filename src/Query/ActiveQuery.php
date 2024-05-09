<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Query;

use Cycle\ActiveRecord\Attribute\Collection;
use Cycle\ActiveRecord\Facade;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;

/**
 * @template-covariant TEntity of object
 *
 * @extends Select<TEntity>
 */
class ActiveQuery extends Select
{
    protected ORMInterface $orm;

    /**
     * @param class-string<TEntity>|non-empty-string $role
     */
    final public function __construct(protected string $role)
    {
        $this->orm = Facade::getOrm();

        parent::__construct($this->orm, $role);
    }

    /**
     * @throws ReflectionException
     */
    public function fetchAll(): iterable
    {
        $reflection = new ReflectionClass(static::class);
        // $reflection = new ReflectionClass($this->class);
        $attributes = $reflection->getAttributes(Collection::class, ReflectionAttribute::IS_INSTANCEOF);

        dd($attributes);

        if ([] === $attributes) {
            return parent::fetchAll();
        }

        $attribute = $attributes[0]->newInstance();
        $collection = $this->orm->getFactory()->collection($attribute->name);

        return $collection->collect($this->getIterator());
    }
}
