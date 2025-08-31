<?php

declare(strict_types=1);

namespace Cycle\Tests\Arch;

use PHPUnit\Architecture\ArchitectureAsserts;
use PHPUnit\Framework\TestCase;

final class DebugTest extends TestCase
{
    use ArchitectureAsserts;

    protected array $excludedPaths = [
        'tests',
        'vendor',
    ];

    public function testForgottenDebugFunctions(): void
    {
        $functions = ['dd', 'exit', 'die', 'var_dump', 'echo', 'print', 'dump', 'tr', 'td', 'error_log', 'trap'];
        $layer = $this->layer();

        foreach ($layer as $object) {
            foreach ($object->uses as $use) {
                foreach ($functions as $function) {
                    $function === $use and throw new \Exception(
                        \sprintf(
                            'Function `%s()` is used in %s.',
                            $function,
                            $object->name,
                        ),
                    );
                }
            }
        }

        $this->assertTrue(true);
    }
}
