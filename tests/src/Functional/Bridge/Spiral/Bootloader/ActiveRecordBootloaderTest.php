<?php

declare(strict_types=1);

namespace Cycle\Tests\Functional\Bridge\Spiral\Bootloader;

use Cycle\App\Testing\TestCase;
use Cycle\ORM\ORMInterface;
use PHPUnit\Framework\Attributes\Test;

final class ActiveRecordBootloaderTest extends TestCase
{
    /**
     * @see - https://github.com/psalm/psalm-plugin-phpunit/issues/144
     */
    #[Test]
    public function it_gets_container_from_static_origin_class(): void
    {
        self::assertTrue($this->getContainer()->has(ORMInterface::class));
    }
}
