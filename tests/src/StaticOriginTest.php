<?php

declare(strict_types=1);

namespace Cycle\Tests;

use Cycle\ActiveRecord\Facade;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORMInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use Spiral\Core\Exception\Container\NotFoundException;

use function var_dump;

final class StaticOriginTest extends TestCase
{
    private MockObject $container;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        Facade::setContainer($this->container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Test]
    public function it_gets_orm_from_static_origin_when_container_has_orm(): void
    {
        $orm = $this->createMock(ORMInterface::class);
        $this->container->method('has')->with(ORMInterface::class)->willReturn(true);
        $this->container->method('get')->with(ORMInterface::class)->willReturn($orm);

        // Assert that the ORM obtained from Facade is the same as the mock
        $this->assertSame($orm, Facade::getOrm());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_throws_exception_when_container_does_not_have_orm(): void
    {
        $this->container->method('has')->with(ORMInterface::class)->willReturn(false);
        $this->container->method('get')->with(ORMInterface::class)->willThrowException(new RuntimeException());

        $this->container->expects($this->once())->method('get')->with(ORMInterface::class)->willThrowException(new NotFoundException());

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
    public function it_gets_entity_manager_from_static_origin(): void
    {
        $orm = $this->createMock(ORMInterface::class);
        $this->container->method('has')->with(ORMInterface::class)->willReturn(true);
        $this->container->method('get')->with(ORMInterface::class)->willReturn($orm);

        $entityManager = Facade::getEntityManager();

        var_dump('em' . $entityManager);
        exit;

        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }
}
