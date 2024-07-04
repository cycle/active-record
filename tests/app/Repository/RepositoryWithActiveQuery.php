<?php

declare(strict_types=1);

namespace Cycle\App\Repository;

use Cycle\ActiveRecord\Repository\ActiveRepository;
use Cycle\App\Entity\User;
use Cycle\App\Query\UserQuery;
use Cycle\Database\Injection\Fragment;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;

/**
 * @extends ActiveRepository<User>
 */
final class RepositoryWithActiveQuery extends ActiveRepository
{
    #[\Override]
    public function __construct()
    {
        parent::__construct(User::class);
    }

    #[\Override]
    public function initSelect(ORMInterface $orm, string $role): Select
    {
        return new UserQuery();
    }

    public function withNameStartLetter(string $letter): static
    {
        return $this->with($this->select()->where(new Fragment('SUBSTRING(name, 1, 1) = ?', $letter)));
    }
}
