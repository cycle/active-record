<?php

declare(strict_types=1);

namespace Cycle\Tests\Bridge\Spiral\Bootloader;

use Cycle\ActiveRecord\Facade;
use Cycle\ORM\ORMInterface;
use Cycle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ActiveRecordBootloaderTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_gets_container_from_static_origin_class(): void
    {
        $this::assertTrue($this->getContainer()->has(ORMInterface::class));
        // @phpstan-ignore-next-line
        $this::assertInstanceOf(ORMInterface::class, Facade::getOrm());
    }
}
