<?php

declare(strict_types=1);

namespace Cycle\Tests\Stub\Repository;

use Cycle\ActiveRecord\Repository\ActiveRepository;
use Cycle\Tests\Stub\Entity\User;
use Cycle\Tests\Stub\Query\UserQuery;
use Cycle\Database\Injection\Fragment;
use Cycle\ORM\ORMInterface;

/**
 * @method UserQuery select()
 * @extends ActiveRepository<User>
 */
final class RepositoryWithActiveQuery extends ActiveRepository
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    #[\Override]
    public function initSelect(ORMInterface $orm, string $role): UserQuery
    {
        return new UserQuery();
    }

    public function withNameStartLetter(string $letter): static
    {
        return $this->with($this->select()->where(new Fragment('SUBSTRING(name, 1, 1) = ?', $letter)));
    }
}
