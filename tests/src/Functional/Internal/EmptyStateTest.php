<?php

declare(strict_types=1);

namespace Cycle\Tests\Functional\Internal;

use Cycle\ActiveRecord\Internal\EmptyState;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EmptyStateTest extends TestCase
{
    #[Test]
    public function it_creates_empty_state_without_error(): void
    {
        $state = new EmptyState();

        self::assertFalse($state->isSuccess());
        self::assertNull($state->getLastError());
    }

    #[Test]
    public function it_creates_empty_state_with_error(): void
    {
        $error = new \Exception('Test error');
        $state = new EmptyState($error);

        self::assertTrue($state->isSuccess());
        self::assertSame($error, $state->getLastError());
    }

    #[Test]
    public function it_returns_self_on_retry(): void
    {
        $state = new EmptyState();
        $retryState = $state->retry();

        self::assertSame($state, $retryState);
    }

    #[Test]
    public function it_returns_self_on_retry_with_error(): void
    {
        $error = new \RuntimeException('Runtime error');
        $state = new EmptyState($error);
        $retryState = $state->retry();

        self::assertSame($state, $retryState);
        self::assertSame($error, $retryState->getLastError());
    }

    #[Test]
    public function it_handles_different_throwable_types(): void
    {
        $error = new \Error('Fatal error');
        $state = new EmptyState($error);

        self::assertTrue($state->isSuccess());
        self::assertSame($error, $state->getLastError());
        self::assertInstanceOf(\Error::class, $state->getLastError());
    }
}
