<?php

declare(strict_types=1);

use Rector\Config;
use Rector\Php81;
use Rector\ValueObject;

return static function (Config\RectorConfig $rectorConfig): void {
    $rectorConfig->cacheDirectory(__DIR__ . '/runtime/rector/');

    $rectorConfig->paths([
        __DIR__ . '/src/',
        __DIR__ . '/tests/',
        __DIR__ . '/.php-cs-fixer.dist.php',
        __DIR__ . '/rector.php',
    ]);

    $rectorConfig->phpVersion(ValueObject\PhpVersion::PHP_82);

    $rectorConfig->rules([
        Php81\Rector\Property\ReadOnlyPropertyRector::class,
    ]);
};
