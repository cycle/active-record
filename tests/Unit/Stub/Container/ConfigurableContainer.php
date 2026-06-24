<?php

declare(strict_types=1);

namespace Cycle\Tests\Unit\Stub\Container;

use Psr\Container\ContainerInterface;

/**
 * A hand-written PSR-11 container whose {@see self::get()} behaviour is supplied as a closure.
 *
 * It replaces the `createMock(ContainerInterface::class)` calls the Facade tests used to rely on:
 * instead of programming a mock's expectations, each test constructs this fake with the exact
 * resolution behaviour it needs (return a service, return `null`, or throw a not-found error) and
 * inspects {@see self::$requested} afterwards to confirm which identifier the Facade asked for.
 *
 * @internal
 */
final class ConfigurableContainer implements ContainerInterface
{
    /** @var list<string> Identifiers that were requested through {@see self::get()}, in order. */
    public array $requested = [];

    /** @var \Closure(string): mixed */
    private \Closure $resolver;

    /**
     * @param \Closure(string): mixed $resolver Produces the value (or throws) for a requested id.
     */
    public function __construct(\Closure $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Build a container that always resolves the given identifier to the provided service.
     */
    public static function returning(string $id, object $service): self
    {
        return new self(static fn(string $requested): object => $requested === $id
            ? $service
            : throw new ServiceNotFoundException(\sprintf('Unexpected service `%s`.', $requested)));
    }

    public function get(string $id): mixed
    {
        $this->requested[] = $id;

        return ($this->resolver)($id);
    }

    public function has(string $id): bool
    {
        return true;
    }
}
