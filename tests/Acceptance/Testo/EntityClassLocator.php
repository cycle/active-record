<?php

declare(strict_types=1);

namespace Cycle\Tests\Acceptance\Testo;

use Spiral\Tokenizer\ClassesInterface;

/**
 * A static {@see ClassesInterface} implementation that exposes a fixed, explicit set of
 * entity classes to the Cycle Annotated locators.
 *
 * The production application discovers entities by scanning the filesystem with the Spiral
 * tokenizer; in tests we feed the locator a curated list instead, which keeps schema compilation
 * fast and fully deterministic — no directory walking, no accidental pick-up of unrelated classes.
 *
 * The Annotated locators only ever call {@see self::getClasses()} without a target, so target
 * filtering is intentionally not implemented.
 *
 * @internal
 */
final readonly class EntityClassLocator implements ClassesInterface
{
    /** @var list<class-string> */
    private array $classes;

    /**
     * @param class-string ...$classes
     */
    public function __construct(string ...$classes)
    {
        $this->classes = \array_values($classes);
    }

    public function getClasses(object|string|null $target = null): array
    {
        $result = [];
        foreach ($this->classes as $class) {
            $reflection = new \ReflectionClass($class);
            $result[$reflection->getName()] = $reflection;
        }

        return $result;
    }
}
