<?php

declare(strict_types=1);

namespace Cycle\Tests\Functional\Bridge\Spiral\Bootloader;

use Cycle\ORM\ORMInterface;
use Cycle\Tests\Functional\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ActiveRecordBootloaderTest extends TestCase
{
    /**
     * @test see - https://github.com/psalm/psalm-plugin-phpunit/issues/144
     */
    #[Test]
    public function it_gets_container_from_static_origin_class(): void
    {
        self::assertTrue($this->getContainer()->has(ORMInterface::class));
    }
}
