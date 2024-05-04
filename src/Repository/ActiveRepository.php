<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Repository;

use Cycle\ActiveRecord\Facade;
use Cycle\ORM\Select;

/**
 * @internal
 *
 * @template TEntity of object
 */
class ActiveRepository
{
    /** @var Select<TEntity> */
    private Select $select;

    /**
     * @param class-string<TEntity> $class
     */
    final public function __construct(string $class)
    {
        $orm = Facade::getOrm();
        $this->select = new Select($orm, $class);
    }

    /**
     * ActiveQuery is always immutable by default.
     */
    public function __clone()
    {
        $this->select = clone $this->select;
    }

    /**
     * Get selector associated with the repository.
     *
     * @return Select<TEntity>
     */
    public function select(): Select
    {
        return clone $this->select;
    }

    public function findByPK(mixed $id): ?object
    {
        return $this->select()->wherePK($id)->fetchOne();
    }

    public function findOne(array $scope = []): ?object
    {
        return $this->select()->fetchOne($scope);
    }

    public function findAll(array $scope = [], array $orderBy = []): iterable
    {
        return $this->select()->where($scope)->orderBy($orderBy)->fetchAll();
    }
}
