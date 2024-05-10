<?php

declare(strict_types=1);

namespace Cycle\App\Entity;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use DateTimeImmutable;
use DateTimeInterface;

#[Entity(table: 'user_identity')]
#[CreatedAt(field: 'createdAt')]
class Identity extends ActiveRecord
{
    #[Column(type: 'bigPrimary', typecast: 'int')]
    public int $id;

    #[Column(type: 'datetime')]
    public DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }
}
