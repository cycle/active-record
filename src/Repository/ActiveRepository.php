<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Repository;

use Cycle\ActiveRecord\Facade;
use Cycle\ORM\Select;

/**
 * @internal
 *
 * @template-covariant TEntity of object
 */
class ActiveRepository
{
    /** @var Select<TEntity> */
    private Select $select;

    /**
     * @param class-string<TEntity> $class
     */
    public function __construct(string $class)
    {
        $orm = Facade::getOrm();
        $this->select = new Select($orm, $class);
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

    /**
     * @return TEntity|null
     */
    public function findByPK(mixed $id): ?object
    {
        return $this->select()->wherePK($id)->fetchOne();
    }

    /**
     * @return TEntity|null
     */
    public function findOne(array $scope = [], array $orderBy = []): ?object
    {
        return $this->select()->orderBy($orderBy)->fetchOne($scope);
    }

    /**
     * @return iterable<TEntity>
     */
    public function findAll(array $scope = [], array $orderBy = []): iterable
    {
        return $this->select()->where($scope)->orderBy($orderBy)->fetchAll();
    }

    /**
     * ActiveQuery is always immutable by default.
     */
    public function __clone()
    {
        $this->select = clone $this->select;
    }
}
