<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class ActiveRecord
{
    /**
     * Finds a single record based on the given primary key.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    final public static function findByPK(mixed $primaryKey): ?static
    {
        /** @var static|null $entity */
        $entity = self::getRepository()->findByPK($primaryKey);

        return $entity;
    }

    /**
     * Finds a single record based on the given scope.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    final public static function findOne(array $scope = []): ?static
    {
        /** @var static|null $entity */
        $entity = self::getRepository()->findOne($scope);

        return $entity;
    }

    /**
     * Finds a single record based on the given scope.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    final public static function findAll(array $scope = []): iterable
    {
        return self::getRepository()->findAll($scope);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    private static function getRepository(): RepositoryInterface
    {
        return self::getOrm()->getRepository(static::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private static function getOrm(): ORMInterface
    {
        return Facade::getOrm();
    }
}