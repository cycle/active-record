<?php

declare(strict_types=1);

namespace Cycle\Tests;

use Cycle\ActiveRecord\Facade;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORMInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

final class FacadeTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Facade::reset();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Test]
    public function it_gets_orm_from_facade_when_container_has_orm(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $orm = $this->createMock(ORMInterface::class);
        $container->method('has')->with(ORMInterface::class)->willReturn(true);
        $container->method('get')->with(ORMInterface::class)->willReturn($orm);

        Facade::setContainer($container);

        // Assert that the ORM obtained from Facade is the same as the mock
        $this::assertSame($orm, Facade::getOrm());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Test]
    public function it_throws_exception_when_container_does_not_have_orm(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container
            ->expects($this::once())
            ->method('get')
            ->with(ORMInterface::class)
            ->willReturn(null);

        Facade::setContainer($container);

        // Assert that a RuntimeException is thrown when the ORM is requested but not available
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The ORM Carrier is not configured.');

        Facade::getOrm();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Test]
    public function it_gets_entity_manager_from_facade(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $orm = $this->createMock(ORMInterface::class);
        $container->method('has')->with(ORMInterface::class)->willReturn(true);
        $container->method('get')->with(ORMInterface::class)->willReturn($orm);

        $entityManager = Facade::getEntityManager();

        $this::assertInstanceOf(EntityManager::class, $entityManager);
    }
}
