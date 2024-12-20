<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord;

use Cycle\ActiveRecord\Exception\ConfigurationException;
use Cycle\Database\DatabaseManager;
use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @internal
 */
class Facade
{
    private static ?ORMInterface $orm = null;

    private static ?EntityManagerInterface $entityManager = null;

    private static ?ContainerInterface $container = null;

    private static ?DatabaseManager $dbal = null;

    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    /**
     * @throws ConfigurationException
     */
    public static function getOrm(): ORMInterface
    {
        return self::$orm ??= self::getFromContainer(ORMInterface::class);
    }

    /**
     * @throws ConfigurationException
     */
    public static function getDatabaseManager(): DatabaseManager
    {
        return self::$dbal ??= self::getFromContainer(DatabaseManager::class);
    }

    public static function getEntityManager(): EntityManagerInterface
    {
        return self::$entityManager ??= new EntityManager(self::getOrm());
    }

    public static function reset(): void
    {
        self::$orm = null;
        self::$dbal = null;
        self::$entityManager = null;
        self::$container = null;
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     *
     * @throws ConfigurationException
     */
    private static function getFromContainer(string $class): object
    {
        // Check if container is set
        self::$container === null and throw new ConfigurationException(
            \sprintf(
                'Container has not been set. Please set the container first using %s method.',
                self::class . '::setContainer()',
            ),
        );

        // Pull service from container
        try {
            return self::$container->get($class);
        } catch (NotFoundExceptionInterface $e) {
            throw new ConfigurationException('Container has no ORMInterface service.', previous: $e);
        } catch (\Throwable $e) {
            throw new ConfigurationException('Failed to get ORMInterface from container.', previous: $e);
        }
    }
}
