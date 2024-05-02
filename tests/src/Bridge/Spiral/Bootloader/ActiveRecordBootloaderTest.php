<?php

declare(strict_types=1);

namespace Cycle\Tests\Bridge\Spiral\Bootloader;

use Cycle\ORM\ORMInterface;
use Cycle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ActiveRecordBootloaderTest extends TestCase
{
    #[Test]
    public function it_gets_container_from_static_origin_class(): void
    {
        $this::assertTrue($this->getContainer()->has(ORMInterface::class));
    }
}
