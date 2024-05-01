<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class Model
{
    /**
     * Finds a single record based on the given scope.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    final public static function findOne(array $scope): ?static
    {
        /** @var static|null $entity */
        $entity = self::getRepository()->findOne($scope);

        return $entity;
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
