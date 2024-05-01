<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord;

use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class Facade
{
    private static ?ORMInterface $orm = null;
    private static ?EntityManagerInterface $entityManager = null;
    private static ?ContainerInterface $container = null;

    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    public static function setOrm(ORMInterface $orm): void
    {
        self::$orm = $orm;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function getOrm(): ORMInterface
    {
        self::$orm ??= self::$container?->get(ORMInterface::class);
        if (null === self::$orm) {
            throw new RuntimeException('The ORM Carrier is not configured.');
        }

        return self::$orm;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function getEntityManager(): EntityManagerInterface
    {
        return self::$entityManager ??= new EntityManager(self::getOrm());
    }

    public static function reset(): void
    {
        self::$orm = null;
        self::$container = null;
    }
}
