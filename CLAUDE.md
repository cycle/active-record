# CLAUDE.md

Guidance for working in this repository.

`cycle/active-record` is an ActiveRecord layer over Cycle ORM. Source lives in `src/` (namespace
`Cycle\ActiveRecord\`). Entities reach the ORM through the static `Cycle\ActiveRecord\Facade`, which
holds a PSR-11 container that resolves `ORMInterface` and `DatabaseManager`.

## Testing

Tests run on **[Testo](https://php-testo.github.io)** through the shared `cycle/testing` harness, not
PHPUnit. Do **not** add `phpunit`, `mockery`, or `extends TestCase` — they have been removed on purpose.

When writing or changing tests, load the `testo-write-tests` and `cycle-testing` skills and follow them.
The essentials:

### Suites & layout

`testo.php` builds the suites with `Cycle\Testing\Config`:

- **Unit** — `tests/Unit/` (namespace `Cycle\Tests\Unit\`). No database server. Where an ORM or a
  `DatabaseManager` is only needed as an object, use `Cycle\Testing\ORM\Memory::orm()` /
  `Cycle\Testing\Database\Memory::manager()`.
- **Acceptance/<Driver>** — one suite per driver (SQLite, MySQL, Postgres, SQLServer) over
  `tests/Acceptance/Common/`, plus `tests/Acceptance/<Driver>/` for driver-only tests. The scenarios live
  in `Acceptance/Common/ActiveRecordTest`; a new scenario is usually a new method there and runs on every
  driver. Exclude a driver with `#[ExceptDriver]` / `#[OnlyDriver]`.

The repo-specific environment is `tests/Stub/OrmEnvironment.php`, wired into `testo.php`: it compiles the
schema of the `tests/Stub/Entity` classes, creates the tables and seeds them **once per suite**, builds
the ORM with entity behaviours, and binds each test's ORM to the `Facade`. Each test runs inside a
transaction that is rolled back (`Isolation::Transaction`). `default` is the suite's driver, `secondary`
is always an in-memory SQLite database.

Acceptance tests extend `Cycle\Testing\ORM\OrmTestCase`: `$this->orm`, `$this->database` (`default`),
`$this->dbal->database('secondary')`, `$this->selectEntity($role, cleanHeap: true)`. The baseline seed is
two users — `Antony` (id `1`) and `John` (id `2`) — plus their identity rows.

Use `#[Isolate(Isolation::None)]` only for the rare test that must observe the absence of an open
transaction. Such a test **must not** mutate committed seed data.

### Test classes instead of mocks

There is no mocking library. Write small hand-rolled fakes that implement the real interface and
exercise the behaviour you need — see `tests/Unit/Stub/Container/ConfigurableContainer.php`
(a PSR-11 container whose `get()` is a closure) used by `FacadeTest` instead of `createMock(...)`.
Never mock `final` classes or enums; instantiate the real type.

### Testo conventions

- Class-level `#[Test]`, `final class`; database tests extend `OrmTestCase` (or `DatabaseTestCase`).
- Assertions: `Testo\Assert`, argument order **actual, expected** — `Assert::same($actual, $expected)`,
  `Assert::count($collection, 3)`, `Assert::instanceOf($object, Foo::class)`. Typed chains:
  `Assert::int($n)->greaterThan(0)`, `Assert::array($a)->hasCount(5)`.
- Expected exceptions: declare `Testo\Expect::exception(X::class)->withMessageContaining(...)` **before**
  the throwing call; the method returns `never`. Use a `try/catch` only when you assert state *after*
  the exception (e.g. that a transaction rolled back).
- Lifecycle: `#[BeforeTest]`/`#[AfterTest]`/`#[BeforeClass]`/`#[AfterClass]` — never `setUp`/`tearDown`.
- Coverage: `#[Covers(...)]` (repeatable) points at concrete classes; the test class declares the set
  it covers.

### Commands

```
composer test            # every suite; a driver whose database is down is skipped
composer test:unit       # Unit suite
composer test:sqlite     # SQLite suites (in-memory, no docker)
composer db:up           # start MySQL, Postgres, SQL Server from the cycle/testing compose file
composer test:mysql      # MySQL suites (also test:postgres, test:sqlserver)
composer db:down
composer test:cc         # SQLite + Unit with coverage; writes runtime/clover.xml (needs Xdebug/PCOV)
```

Connection settings and ports come from `vendor/cycle/testing/resources/docker/.env`; exported `DB_*`
variables override them (`DB_REQUIRED=1` turns an unreachable database into a failure). Always pass
`--json` to `vendor/bin/testo` when you need a machine-readable result.

## Static analysis & style

```
composer psalm      # Psalm (analyses src/ only)
composer cs:fix     # PHP-CS-Fixer
composer refactor   # Rector
composer infect     # Infection (mutation testing, testFramework = testo)
```
