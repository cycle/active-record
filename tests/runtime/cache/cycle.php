<?php

declare(strict_types=1);

return [
    'user' => [
        1 => 'Cycle\\App\\Entity\\User',
        2 => 'Cycle\\ORM\\Mapper\\Mapper',
        3 => 'Cycle\\ORM\\Select\\Source',
        4 => 'Cycle\\ORM\\Select\\Repository',
        5 => 'default',
        6 => 'user',
        7 => [
            0 => 'id',
        ],
        8 => [
            0 => 'id',
        ],
        9 => [
            'id' => 'id',
            'name' => 'name',
        ],
        10 => [
            'identity' => [
                0 => 12,
                1 => 'identity',
                3 => 11,
                2 => [
                    30 => true,
                    31 => false,
                    33 => [
                        0 => 'id',
                    ],
                    32 => [
                        0 => 'id',
                    ],
                ],
            ],
        ],
        12 => null,
        13 => [
            'id' => 'int',
        ],
        14 => [
        ],
        19 => null,
        20 => [
            'id' => 2,
        ],
    ],
    'identity' => [
        1 => 'Cycle\\App\\Entity\\Identity',
        2 => 'Cycle\\ORM\\Mapper\\Mapper',
        3 => 'Cycle\\ORM\\Select\\Source',
        4 => 'Cycle\\ORM\\Select\\Repository',
        5 => 'default',
        6 => 'identity',
        7 => [
            0 => 'id',
        ],
        8 => [
            0 => 'id',
        ],
        9 => [
            'id' => 'id',
            'createdAt' => 'created_at',
        ],
        10 => [
        ],
        12 => null,
        13 => [
            'id' => 'int',
            'createdAt' => 'datetime',
        ],
        14 => [
        ],
        19 => null,
        20 => [
            'id' => 2,
        ],
    ],
];
