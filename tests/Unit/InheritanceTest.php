<?php

declare(strict_types=1);

namespace Cycle\Tests\Unit;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\ActiveRecord\Query\ActiveQuery;
use Cycle\ORM\RepositoryInterface;
use Testo\Assert;
use Testo\Codecov\Covers;
use Testo\Test;

/**
 * Verifies that {@see ActiveRecord} can be extended and its static API overridden — no database is
 * involved, the class merely has to be definable and instantiable.
 */
#[Test]
#[Covers(ActiveRecord::class)]
final class InheritanceTest
{
    public function activeRecordCanBeExtendedAndOverridden(): void
    {
        $entity = new class extends ActiveRecord {
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

        Assert::instanceOf($entity, ActiveRecord::class);
    }
}
