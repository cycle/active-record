# CLAUDE.md

Guidance for working in this repository.

`cycle/active-record` is an ActiveRecord layer over Cycle ORM. Source lives in `src/` (namespace
`Cycle\ActiveRecord\`). Entities reach the ORM through the static `Cycle\ActiveRecord\Facade`, which
holds a PSR-11 container that resolves `ORMInterface` and `DatabaseManager`.

## Testing

Tests run on **[Testo](https://php-testo.github.io)**, not PHPUnit. Do **not** add `phpunit`,
`mockery`, or `extends TestCase` — they have been removed on purpose.

When writing or changing tests, load the `testo-write-tests` skill and follow it. The essentials:

### Suites & layout

Two suites are declared in `testo.php`:

- **Unit** — `tests/src/Unit/` (namespace `Cycle\Tests\Unit\`). Driver-agnostic, no database server.
  Where a SQLite in-memory ORM is needed, build one with `OrmEnvironment::forDriver(...)`.
- **Acceptance** — only the concrete per-driver classes under `tests/src/Acceptance/Driver/<Driver>/`
  are discovered. The actual scenarios live in the abstract `Acceptance/Common/ActiveRecordTestCase`
  and run once per driver. Shared test harness is in `tests/src/Acceptance/Testo/`.

Mirror the structure: a new driver-bound scenario is usually **a new method on the abstract
`ActiveRecordTestCase`** — it then runs on every driver automatically.

### `#[Group('driver-*')]` is mandatory for DB-bound tests

The `DatabasePlugin` (registered on the Acceptance suite) provisions the database **based on the
driver group**:

- The abstract case carries `#[Group('driver')]`; each concrete class carries `#[Group('driver-sqlite')]`,
  `driver-mysql`, `driver-pgsql`, or `driver-sqlserver`.
- For each test the plugin's interceptor resolves the driver from that group, builds the schema/seed
  **once per driver**, binds a fresh ORM to the `Facade` (via a container `scope`), and wraps the test
  in a transaction that is **rolled back** afterwards. So a test never recreates tables and never leaks
  state.
- A test class with **no** `driver-*` group gets no database — put database-touching tests in the
  Acceptance suite with the proper group, not in Unit.

Inside a scenario, read the environment back from the Facade through the base helpers:
`$this->orm()`, `$this->database('default'|'secondary')`, `$this->selectEntity($role, cleanHeap: true)`.
The baseline seed is two users — `Antony` (id `1`) and `John` (id `2`) — plus their identity rows.

Use `#[WithoutTransaction]` (from `Cycle\Tests\Acceptance\Testo`) only for the rare test that must
observe the absence of an open transaction. Such a test **must not** mutate committed seed data.

### Test classes instead of mocks

There is no mocking library. Write small hand-rolled fakes that implement the real interface and
exercise the behaviour you need — see `tests/src/Unit/Stub/Container/ConfigurableContainer.php`
(a PSR-11 container whose `get()` is a closure) used by `FacadeTest` instead of `createMock(...)`.
Never mock `final` classes or enums; instantiate the real type.

### Testo conventions

- Class-level `#[Test]`, `final class`, no base class for the concrete test.
- Assertions: `Testo\Assert`, argument order **actual, expected** — `Assert::same($actual, $expected)`,
  `Assert::count($collection, 3)`, `Assert::instanceOf($object, Foo::class)`. Typed chains:
  `Assert::int($n)->greaterThan(0)`, `Assert::array($a)->hasCount(5)`.
- Expected exceptions: declare `Testo\Expect::exception(X::class)->withMessageContaining(...)` **before**
  the throwing call; the method returns `never`. Use a `try/catch` only when you assert state *after*
  the exception (e.g. that a transaction rolled back).
- Lifecycle: `#[BeforeTest]`/`#[AfterTest]`/`#[BeforeClass]`/`#[AfterClass]` — never `setUp`/`tearDown`.
- Coverage: `#[Covers(...)]` (repeatable) points at concrete classes; the abstract `ActiveRecordTestCase`
  declares the set it covers and the concrete driver classes inherit it.

### Commands

```
composer test            # full run (all suites)
composer test:unit       # Unit suite
composer test:no-driver  # everything except driver-bound tests (no DB needed)
composer test:sqlite     # Acceptance against SQLite (in-memory)
composer test:mysql      # Acceptance against MySQL  (needs the docker-compose service)
composer test:pgsql      # Acceptance against PostgreSQL
composer test:sqlserver  # Acceptance against SQL Server
composer test:cc         # run with coverage; writes runtime/clover.xml (needs Xdebug/PCOV)
```

Real databases for the driver suites come from `tests/docker-compose.yml`; connection parameters are
read from `DB_HOSTNAME` / `DB_PORT` / `DB_USER` / `DB_PASSWORD` / `DB_DATABASE` (see
`Acceptance/Testo/DatabaseDriver`). Always pass `--json` to `vendor/bin/testo` when you need a
machine-readable result.

## Static analysis & style

```
composer psalm      # Psalm (analyses src/ only)
composer cs:fix     # PHP-CS-Fixer
composer refactor   # Rector
composer infect     # Infection (mutation testing, testFramework = testo)
```
