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

[![Codecov Coverage](https://img.shields.io/codecov/c/github/cycle/active-record?style=flat-square&logo=codecov)](https://app.codecov.io/gh/cycle/active-record)
[![Type Coverage](https://shepherd.dev/github/cycle/active-record/coverage.svg)](https://shepherd.dev/github/cycle/active-record)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat-square&label=mutation%20score&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fcycle%2Factive-record%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/cycle/active-record/master)
[![MetaStorm plugin](https://img.shields.io/static/v1?label=Meta+Storm&message=annotated&color=008880&style=flat-square&logo=data:image/svg%2bxml;base64,PHN2ZyB3aWR0aD0iMTExIiBoZWlnaHQ9IjExMSIgdmlld0JveD0iMCAwIDExMSAxMTEiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTYwLjQ2MTcgOEwzMC4xNjU0IDguMjQ3OUMyNy4yNDYgOC4yNzE3OSAyNC42NDggMTAuMTA1IDIzLjY0NyAxMi44NDc1TDAuNDMwNTkgNzYuODMxNEMtMS4yNDQ1OSA4MS40MjA4IDIuMTc4MDUgODYuMjcxMyA3LjA2MzUzIDg2LjIzMTNMMzIuMTE5NyA4Ni4wMjYzTDM1LjQxNDcgNzYuOTk5TDkuODczOTEgNzcuMjA4TDMxLjYyNTkgMTcuMjM2Mkw1Ny4xNjY2IDE3LjAyNzNMNjAuNDYxNyA4WiIgZmlsbD0iI0FGQjFCMyIvPjxwYXRoIGQ9Ik00OS40MTQ5IDEwMi45OTlMNzkuNzEyMiAxMDIuOTk5QzgyLjYzMTcgMTAyLjk5OSA4NS4yNDQ2IDEwMS4xODcgODYuMjY4IDk4LjQ1MjlMMTEwLjAxMyAzNC40NTI0QzExMS43MjYgMjkuODc2OCAxMDguMzQzIDI0Ljk5ODUgMTAzLjQ1NyAyNC45OTg1TDc4LjQwMDQgMjQuOTk4NUw3NS4wMzE2IDMzLjk5ODVMMTAwLjU3MyAzMy45OTg1TDc4LjMyNTMgOTMuOTk5TDUyLjc4MzcgOTMuOTk5TDQ5LjQxNDkgMTAyLjk5OVoiIGZpbGw9IiNBRkIxQjMiLz48cGF0aCBkPSJNMjIgNjFMODYgOEw1NyA1MEw4OS45OTk3IDQ5Ljk5OTdMMjYgMTAzTDU1IDYxSDIyWiIgZmlsbD0iI0M3NTQ1MCIvPjwvc3ZnPg==)](https://github.com/xepozz/meta-storm-idea-plugin)

[![Discord](https://img.shields.io/discord/538114875570913290?style=flat-square&logo=discord&labelColor=7289d9&logoColor=white&color=39456d)](https://discord.gg/spiralphp)
[![Follow on Twitter (X)](https://img.shields.io/badge/-Follow-black?style=flat-square&logo=X)](https://x.com/intent/follow?screen_name=SpiralPHP)

[//]: # ([![Commits since latest release]&#40;https://img.shields.io/github/commits-since/cycle/active-record/latest?style=flat-square&#41;]&#40;https://packagist.org/packages/cycle/active-record&#41;)

</div>

# Active Record Implementation for Cycle ORM

This library extends Cycle ORM by integrating the [Active Record pattern](https://en.wikipedia.org/wiki/Active_record_pattern), providing developers with an intuitive, object-centric way to interact with databases.

Unlike Cycle ORM's default Data Mapper pattern, which separates the in-memory object representations from database operations, Active Record combines data access and business logic in a single entity.

This allows for more straightforward and rapid development cycles, particularly for simpler CRUD operations, by enabling direct data manipulation through the object's properties and methods.

<br>

## Prerequisites

Before you begin, ensure your development environment meets the following requirements:

- **PHP Version:** 8.1 or higher
- One of the Cycle ORM adapters:
  - [`spiral/cycle-bridge`](https://github.com/spiral/cycle-bridge) official Cycle ORM adapter for the [Spiral Framework](https://github.com/spiral/framework)
  - [`yiisoft/yii-cycle`](https://github.com/yiisoft/yii-cycle) — official Cycle ORM adapter for the [Yii 3](https://www.yiiframework.com)
  - [`wayofdev/laravel-cycle-orm-adapter`](https://github.com/wayofdev/laravel-cycle-orm-adapter) — package managed by [@wayofdev](https://github.com/wayofdev) for the [Laravel](https://laravel.com) 10.x or higher.

<br>

## Installation

The preferred way to install this package is through [Composer](https://getcomposer.org/).

```bash
composer require cycle/active-record
```

[![PHP Version Require](https://poser.pugx.org/cycle/active-record/require/php?style=flat-square)](https://packagist.org/packages/cycle/active-record)
[![Latest Stable Version](https://img.shields.io/packagist/v/cycle/active-record?&style=flat-square)](https://packagist.org/packages/cycle/active-record)
[![License](https://img.shields.io/packagist/l/cycle/active-record.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/cycle/active-record?&style=flat-square)](https://packagist.org/packages/cycle/active-record)

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

## Usage

> [!NOTE]  
> For detailed usage instructions, refer to the [documentation][Documentation].

### → Basic Example

#### Define Entity with ActiveRecord

```php
use Cycle\ActiveRecord\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(table: 'users')]
class User extends ActiveRecord
{
    #[Column(type: 'primary', typecast: 'int')]
    public ?int $id = null;

    #[Column(type: 'string')]
    public string $name;

    public function create(string $name)
    {
        return self::make([
            'name' => $name,
        ]);
    }
}
```

#### Create a new record

```php
$user = User::create(name: 'John');
$user->saveOrFail();
```

### → Advanced Usage Examples

#### Query Builder Integration

```php
// Find users with advanced Cycle ORM filtering
$user = User::query()
    ->where('name', 'John')
    ->where('active', true)
    ->fetchOne();

// Find by primary key
$user = User::findByPK(42);

// Find with conditions
$users = User::findAll(['status' => 'active']);
$user = User::findOne(['email' => 'john@example.com']);
```

#### Batch Operations and Transactions

```php
$user1 = new User('Alice');
$user2 = new User('Bob');

// Group multiple operations in a single transaction
ActiveRecord::groupActions(function (EntityManagerInterface $em) use ($user1, $user2) {
    $user1->save();
    $user2->save();
    // Both users saved in one transaction
}, TransactionMode::OpenNew);

// Advanced transaction handling
User::transact(function (DatabaseInterface $db, EntityManagerInterface $em) {
    $user = User::query()->forUpdate()->fetchOne(['name' => 'Charlie']);
    $user->name = 'Charles';
    $user->save();
});
```

<br>

## Want to Contribute?

Thank you for considering contributing to the cycle community! We are open to all kinds of contributions. If you want to:

- 🤔 [Suggest a feature](https://github.com/cycle/active-record/issues/new?assignees=&labels=type%3A+enhancement&projects=&template=2-feature-request.yml&title=%5BFeature%5D%3A+)
- 🐛 [Report an issue](https://github.com/cycle/active-record/issues/new?assignees=&labels=type%3A+documentation%2Ctype%3A+maintenance&projects=&template=1-bug-report.yml&title=%5BBug%5D%3A+)
- 📖 [Improve documentation](https://github.com/cycle/active-record/issues/new?assignees=&labels=type%3A+documentation%2Ctype%3A+maintenance&projects=&template=4-docs-bug-report.yml&title=%5BDocs%5D%3A+)
- 👨‍💻 Contribute to the code

You are more than welcome. Before contributing, kindly check our [contribution guidelines](.github/CONTRIBUTING.md).

[![Conventional Commits](https://img.shields.io/badge/Conventional%20Commits-1.0.0-yellow.svg?style=for-the-badge)](https://conventionalcommits.org)
[![Contributors](https://img.shields.io/github/contributors/cycle/active-record?style=for-the-badge)](https://github.com/cycle/active-record/graphs/contributors)

[Documentation]: https://cycle-orm.dev/docs/active-record-introduction/
