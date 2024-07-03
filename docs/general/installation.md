# Installation

### 🚩 Prerequisites

Before you begin, ensure your development environment meets the following requirements:

* **PHP Version:** 8.2 or higher
* **Composer**
* One of the Cycle ORM adapters:
* [`spiral/cycle-bridge`](https://github.com/spiral/cycle-bridge) official Cycle ORM adapter for the [Spiral Framework](https://github.com/spiral/framework)
* [`yiisoft/yii-cycle`](https://github.com/yiisoft/yii-cycle) — official Cycle ORM adapter for the [Yii 3](https://www.yiiframework.com)
* [`wayofdev/laravel-cycle-orm-adapter`](https://github.com/wayofdev/laravel-cycle-orm-adapter) — package managed by [@wayofdev](https://github.com/wayofdev) for the [Laravel](https://laravel.com) 10.x or higher.



### 💿 Installation

The preferred way to install this package is through [Composer](https://getcomposer.org/).

```bash
composer require cycle/active-record
```

After package install you need to, optionally, register bootloader / service-provider in your application.



### 🔧 Framework-Specific Configuration

#### → Spiral Framework

If you are installing the package on the Spiral Framework with the \[spiral-packages/discoverer]\(https://github.com/spiral-packages/discoverer) package, then you don't need to register Bootloader by yourself. It will be registered automatically.

Otherwise, update the Bootloader list in your application configuration:

```php
<?php

declare(strict_types=1);

namespace App\Application;

use Spiral\Cycle\Bootloader as CycleBridge;
use Cycle\ActiveRecord\Bridge\Spiral\Bootloader\ActiveRecordBootloader;

class Kernel extends \Spiral\Framework\Kernel
{
    public function defineBootloaders(): array
    {
        return [
            // ...

            // ORM
            CycleBridge\SchemaBootloader::class,
            CycleBridge\CycleOrmBootloader::class,
            CycleBridge\AnnotatedBootloader::class,

            // ActiveRecord
            ActiveRecordBootloader::class,

            // ...
        ];
}
```

For more information about bootloaders, refer to the [Spiral Framework documentation](https://spiral.dev/docs/framework-bootloaders/current).

#### → Laravel

If you are using Laravel, then you don't need to register service-provider by yourself. It will be registered automatically.

#### → Yii 3

For configuration instructions refer to [yii-cycle installation guide](https://github.com/yiisoft/yii-cycle/blob/master/docs/guide/en/installation.md).

#### → Other Frameworks

This package uses [PSR-11](https://www.php-fig.org/psr/psr-11/) compatible `container` to resolve dependencies. After container initialization you need to pass `container` instance to the static facade:

```php
\Cycle\ActiveRecord\Facade::setContainer($container);
```
