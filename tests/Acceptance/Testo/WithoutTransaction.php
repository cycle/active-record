<?php

declare(strict_types=1);

namespace Cycle\Tests\Acceptance\Testo;

/**
 * Opts a test (or a whole test class) out of the automatic per-test database transaction applied by
 * {@see DatabaseInterceptor}.
 *
 * By default every acceptance test runs inside a transaction that is rolled back afterwards, which
 * keeps the seeded data intact without recreating tables. A few tests need to observe behaviour that
 * depends on there being *no* open transaction (e.g. asserting that a "current transaction" mode
 * throws when none is active). Mark those with this attribute.
 *
 * Contract: a test marked with this attribute MUST NOT mutate the committed seed data, because its
 * changes are not rolled back. In practice such tests fail fast (before any write is committed).
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final readonly class WithoutTransaction {}
