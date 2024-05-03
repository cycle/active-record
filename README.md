<div align="center">
    <br>
    <a href="https://cycle-orm.dev" target="_blank">
        <picture>
            <source media="(prefers-color-scheme: dark)" srcset="https://github.com/cycle/.github/blob/main/logo/words-vector-dark.svg?raw=true">
            <img width="50%" align="center" src="https://github.com/cycle/.github/blob/main/logo/words-vector-light.svg?raw=true" alt="CycleORM Logo">
        </picture>
    </a>
    <br>
    <br>
</div>

<div align="center">

[![Build Status](https://img.shields.io/endpoint.svg?url=https%3A%2F%2Factions-badge.atrox.dev%2Fwayofdev%2Factive-record%2Fbadge&style=flat-square&label=github%20actions)](https://github.com/wayofdev/active-record/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/wayofdev/active-record?&style=flat-square)](https://packagist.org/packages/wayofdev/active-record)
[![Latest Stable Version](https://img.shields.io/packagist/v/wayofdev/active-record?&style=flat-square)](https://packagist.org/packages/wayofdev/active-record)
[![Commits since latest release](https://img.shields.io/github/commits-since/wayofdev/active-record/latest?style=flat-square)](https://packagist.org/packages/wayofdev/active-record)
[![PHP Version Require](https://poser.pugx.org/wayofdev/active-record/require/php?style=flat-square)](https://packagist.org/packages/wayofdev/active-record)
[![Codecov Coverage](https://img.shields.io/codecov/c/github/wayofdev/active-record?style=flat-square&logo=codecov)](https://app.codecov.io/gh/wayofdev/active-record)
[![Type Coverage](https://shepherd.dev/github/wayofdev/active-record/coverage.svg)](https://shepherd.dev/github/wayofdev/active-record)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat-square&label=mutation%20score&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fwayofdev%2Factive-record%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/wayofdev/active-record/master)
![PHP Stan Level 5 of 9](https://img.shields.io/badge/phpstan%20level-5%20of%209-yellowgreen?style=flat-square)
[![Discord](https://img.shields.io/discord/538114875570913290?style=flat-square&logo=discord&labelColor=7289d9&logoColor=white&color=39456d)](https://discord.gg/spiralphp)
[![Follow on Twitter (X)](https://img.shields.io/badge/-Follow-black?style=flat-square&logo=X)](https://x.com/intent/follow?screen_name=SpiralPHP)

</div>

# Active Record Implementation for Cycle ORM

This library extends Cycle ORM by integrating the [Active Record pattern](https://en.wikipedia.org/wiki/Active_record_pattern), providing developers with an intuitive, object-centric way to interact with databases.

Unlike Cycle ORM's default Data Mapper pattern, which separates the in-memory object representations from database operations, Active Record combines data access and business logic in a single entity.

This allows for more straightforward and rapid development cycles, particularly for simpler CRUD operations, by enabling direct data manipulation through the object's properties and methods.

<br>

## 🚩 Prerequisites

Before you begin, ensure your development environment meets the following requirements:

- **PHP Version:** 8.2 or higher
- One of the Cycle ORM adapters:
  - [`spiral/cycle-bridge`](https://github.com/spiral/cycle-bridge) official Cycle ORM adapter for the [Spiral Framework](https://github.com/spiral/framework)
  - [`yiisoft/yii-cycle`](https://github.com/yiisoft/yii-cycle) — official Cycle ORM adapter for the [Yii 3](https://www.yiiframework.com)
  - [`wayofdev/laravel-cycle-orm-adapter`](https://github.com/wayofdev/laravel-cycle-orm-adapter) —package managed by [@wayofdev](https://github.com/wayofdev) for the [Laravel](https://laravel.com) 10.x or higher.

<br>

## 💿 Installation

The preferred way to install this package is through [Composer](https://getcomposer.org/).

```bash
composer require cycle/active-record
```

After package install you need to, optionally, register bootloader / service-provider in your application.

### → Spiral Framework

> [!NOTE]  
> If you are installing the package on the Spiral Framework with the [spiral-packages/discoverer](https://github.com/spiral-packages/discoverer) package, then you don't need to register bootloader by yourself. It will be registered automatically.

Update Bootloader list in your application configuration:

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

### → Laravel

> [!NOTE]
> If you are using Laravel, then you don't need to register service-provider by yourself. It will be registered automatically.

### → Yii 3

For configuration instructions refer to [yii-cycle installation guide](https://github.com/yiisoft/yii-cycle/blob/master/docs/guide/en/installation.md).

### → Other Frameworks

This package uses [PSR-11](https://www.php-fig.org/psr/psr-11/) compatible `container` to resolve dependencies. After container initialization you need to pass `container` instance to the static facade:

```php
\Cycle\ActiveRecord\Facade::setContainer($container);
```

## 📖 Usage

@todo

<br>

## 🧪 Running Tests

### → PHPUnit tests

To run tests, run the following command:

```bash
make test
```

### → Mutation tests

To run mutation tests, using [`infection/infection`](https://github.com/infection/infection):

```bash
make infect
```

### → Static Analysis

Code quality using PHPStan:

```bash
make lint-stan
```

and using Psalm:

```bash
make lint-psalm
```

### → Coding Standards Fixing

Fix code using The PHP Coding Standards Fixer (PHP CS Fixer) to follow our standards:

```bash
make lint-php
```

### → Lint Yaml files

Lint all yaml files in project:

```bash
make lint-yaml
```

### → Lint Markdown files

Lint all yaml files in project:

```bash
make lint-md
```

### → Lint GitHub Actions

Lint all yaml files in project:

```bash
make lint-actions
```

<br>

## 🔒 Security Policy

This project has a [security policy](.github/SECURITY.md).

<br>

## 🙌 Want to Contribute?

Thank you for considering contributing to the wayofdev community! We are open to all kinds of contributions. If you want to:

- 🤔 [Suggest a feature](https://github.com/wayofdev/active-record/issues/new?assignees=&labels=type%3A+enhancement&projects=&template=2-feature-request.yml&title=%5BFeature%5D%3A+)
- 🐛 [Report an issue](https://github.com/wayofdev/active-record/issues/new?assignees=&labels=type%3A+documentation%2Ctype%3A+maintenance&projects=&template=1-bug-report.yml&title=%5BBug%5D%3A+)
- 📖 [Improve documentation](https://github.com/wayofdev/active-record/issues/new?assignees=&labels=type%3A+documentation%2Ctype%3A+maintenance&projects=&template=4-docs-bug-report.yml&title=%5BDocs%5D%3A+)
- 👨‍💻 Contribute to the code

You are more than welcome. Before contributing, kindly check our [contribution guidelines](.github/CONTRIBUTING.md).

[![Conventional Commits](https://img.shields.io/badge/Conventional%20Commits-1.0.0-yellow.svg?style=for-the-badge)](https://conventionalcommits.org)

<br>

## 🫡 Contributors

<a href="https://github.com/wayofdev/active-record/graphs/contributors">
    <img align="left" src="https://img.shields.io/github/contributors-anon/wayofdev/active-record?style=for-the-badge" alt="Contributors Badge"/>
</a>

<br>
<br>

## 🌐 Social Links

- **Twitter:** Follow our organization [@SpiralPHP](https://twitter.com/intent/follow?screen_name=spiralphp).
- **Discord:** Join our community on [Discord](https://discord.gg/SpiralPHP).

<br>

## ⚖️ License

[![Licence](https://img.shields.io/github/license/wayofdev/active-record?style=for-the-badge&color=blue)](./LICENSE.md)

<br>
