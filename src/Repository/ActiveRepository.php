<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Repository;

use Cycle\ActiveRecord\Facade;
use Cycle\ActiveRecord\Query\ActiveQuery;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;
use Cycle\ORM\Select;

/**
 * ActiveRepository combines the advantages of the {@see RepositoryInterface} and the {@see ActiveQuery}:
 *
 * - Immutable by default
 * - Can be associated with an entity, or not
 * - No restriction "an entity can have only one repository"
 * - Not a QueryBuilder entity, so it can follow a contract with a limited set of methods.
 * - Organically used in the DI container
 *
 * @see self::forUpdate() as an example of immutabile method.
 *
 * @internal
 *
 * @template-covariant TEntity of object
 */
class ActiveRepository
{
    /** @var Select<TEntity> */
    private Select $select;

    /**
     * Redefine the constructor in the child class to set the default entity class:
     *
     * ```php
     * public function __construct()
     * {
     *     parent::__construct(User::class);
     * }
     * ```
     *
     * @param class-string<TEntity> $role
     */
    public function __construct(string $role)
    {
        $orm = Facade::getOrm();
        $this->select = $this->initSelect($orm, $role);
    }

    /**
     * Find entity by primary key.
     *
     * @note Limit of 1 will be added to the query.
     *
     * @return TEntity|null
     */
    public function findByPK(mixed $id): ?object
    {
        return $this->select()->wherePK($id)->fetchOne();
    }

    /**
     * Find a single record based on the given scope.
     *
     * @note Limit of 1 will be added to the query.
     *
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
     * @return $this
     * @mutation-free
     */
    public function forUpdate(): static
    {
        return $this->with($this->select()->forUpdate());
    }

    /**
     * Get a copy of the current selector associated with the repository.
     *
     * @note The method is final to prevent the selector from being modified.
     *       If you need to modify the default selector, consider using a constructor or {@see self::initSelect()}.
     *
     * @return Select<TEntity>
     * @mutation-free
     */
    final public function select(): Select
    {
        return clone $this->select;
    }

    /**
     * @param Select<TEntity> $select
     *
     * @return $this
     */
    protected function with(Select $select): static
    {
        $repository = clone $this;
        $repository->select = $select;
        return $repository;
    }

    /**
     * Create a default selector for the repository. Used in the constructor.
     *
     * How to modify the default selector:
     *
     * ```php
     * public function initSelect(ORMInterface $orm, string $role)
     * {
     *     parent::initSelect($orm, $role)->where('deleted_at', '=', null);
     * }
     * ```
     *
     * How to use {@see ActiveQuery} instead of {@see Select}:
     *
     * ```php
     * protected function initSelect(ORMInterface $orm, string $role): Select
     * {
     *     return new CustomActiveQuery($role);
     * }
     * ```
     *
     * @param class-string<TEntity> $role
     *
     * @return Select<TEntity>
     */
    protected function initSelect(ORMInterface $orm, string $role): Select
    {
        return new Select($orm, $role);
    }
}
