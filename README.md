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
[![Software License](https://img.shields.io/github/license/wayofdev/active-record.svg?style=flat-square&color=blue)](LICENSE.md)
[![Commits since latest release](https://img.shields.io/github/commits-since/wayofdev/active-record/latest?style=flat-square)](https://github.com/wayofdev/active-record)
[![Discord](https://img.shields.io/discord/538114875570913290?style=flat-square&logo=discord&labelColor=7289d9&logoColor=white&color=39456d)](https://discord.gg/spiralphp)
[![Twitter](https://img.shields.io/badge/-Follow-black?style=flat-square&logo=X)](https://x.com/intent/follow?screen_name=SpiralPHP)

</div>

# Active Record Implementation for Cycle ORM

Active Record Pattern implementation for Cycle ORM. This package provides a simple way to work with your database records using Active Record pattern.

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

After package install you need to register bootloader / service-provider in your application.

<br>
