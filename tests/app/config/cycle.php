<?php

declare(strict_types=1);

use Cycle\ORM\SchemaInterface;

return [
    'schema' => [
        /*
         * true (Default) - Schema will be stored in a cache after compilation.
         * It won't be changed after entity modification. Use `php app.php cycle` to update schema.
         *
         * false - Schema won't be stored in a cache after compilation.
         * It will be automatically changed after entity modification. (Development mode)
         */
        'cache' => false,

        /*
         * The CycleORM provides the ability to manage default settings for
         * every schema with not defined segments
         */
        'defaults' => [
            SchemaInterface::MAPPER => Cycle\ORM\Mapper\Mapper::class,
            SchemaInterface::REPOSITORY => Cycle\ORM\Select\Repository::class,
            SchemaInterface::SCOPE => null,
            SchemaInterface::TYPECAST_HANDLER => [
                Cycle\ORM\Parser\Typecast::class,
            ],
        ],

        'collections' => [
            'default' => 'array',
            'factories' => [
                'array' => new Cycle\ORM\Collection\ArrayCollectionFactory(),
                // 'doctrine' => new \Cycle\ORM\Collection\DoctrineCollectionFactory(),
                // 'illuminate' => new \Cycle\ORM\Collection\IlluminateCollectionFactory(),
            ],
        ],

        /*
         * Schema generators (Optional)
         * null (default) - Will be used schema generators defined in bootloaders
         */
        'generators' => [
            Cycle\Schema\Generator\ResetTables::class,
            Cycle\Annotated\Embeddings::class,
            Cycle\Annotated\Entities::class,
            Cycle\Annotated\TableInheritance::class,
            Cycle\Annotated\MergeColumns::class,
            Cycle\Schema\Generator\GenerateRelations::class,
            Cycle\Schema\Generator\GenerateModifiers::class,
            Cycle\Schema\Generator\ValidateEntities::class,
            Cycle\Schema\Generator\RenderTables::class,
            Cycle\Schema\Generator\RenderRelations::class,
            Cycle\Schema\Generator\RenderModifiers::class,
            Cycle\Annotated\MergeIndexes::class,
            Cycle\Schema\Generator\SyncTables::class,
            Cycle\Schema\Generator\GenerateTypecast::class,
        ],
    ],

    /*
     * Prepare all internal ORM services (mappers, repositories, typecasters...)
     */
    'warmup' => false,
];
