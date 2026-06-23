<?php

declare(strict_types=1);

namespace Cycle\App\Entity;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * Entity that always lives in the `secondary` (in-memory SQLite) database,
 * independent of the driver used for the default database. Useful for
 * multi-database tests.
 */
#[Entity(table: 'post', database: 'secondary')]
class Post extends ActiveRecord
{
    #[Column(type: 'primary', typecast: 'int')]
    public int $id;

    #[Column(type: 'string')]
    public string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }
}
