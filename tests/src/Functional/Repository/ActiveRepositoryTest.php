<?php

declare(strict_types=1);

namespace Cycle\Tests\Functional\Repository;

use Cycle\ActiveRecord\Repository\ActiveRepository;
use Cycle\App\Entity\User;
use Cycle\App\Repository\RepositoryWithActiveQuery;
use Cycle\Tests\Functional\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Test;

final class ActiveRepositoryTest extends DatabaseTestCase
{
    #[Test]
    public function it_extends_repository_constructor(): void
    {
        $repository = new class extends ActiveRepository {
            public function __construct()
            {
                parent::__construct(User::class);
            }
        };

        self::assertInstanceOf(User::class, $repository->findOne());
    }

    #[Test]
    public function it_fetches_one_entity(): void
    {
        $repository = new ActiveRepository(User::class);

        $user = $repository->findOne(['id' => 2]);

        self::assertInstanceOf(User::class, $user);
        self::assertSame(2, $user->id);
    }

    #[Test]
    public function it_fetches_one_entity_by_pk(): void
    {
        $repository = new ActiveRepository(User::class);

        $user = $repository->findByPK(2);

        self::assertInstanceOf(User::class, $user);
        self::assertSame(2, $user->id);
    }

    #[Test]
    public function it_uses_custom_repository_with_active_query(): void
    {
        $repository = new RepositoryWithActiveQuery();
        $letter = 'J';

        $user = $repository->withNameStartLetter($letter)->findOne();

        self::assertInstanceOf(User::class, $user);
        self::assertSame($letter, $user->name[0]);
    }
}
