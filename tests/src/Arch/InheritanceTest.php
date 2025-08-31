<?php

declare(strict_types=1);


use Cycle\ActiveRecord\ActiveRecord;
use Cycle\ActiveRecord\Query\ActiveQuery;
use Cycle\ORM\RepositoryInterface;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Test that AR classes can be extended and methods can be overridden.
 */
final class InheritanceTest extends TestCase
{
    #[Test]
    #[DoesNotPerformAssertions]
    public function inheritActiveRecord(): void
    {
        new class extends ActiveRecord {
            public static function make(array $data): static
            {
                return parent::make($data);
            }

            public static function query(): ActiveQuery
            {
                return parent::query();
            }

            public static function getRepository(): RepositoryInterface
            {
                return parent::getRepository();
            }
        };
    }
}
