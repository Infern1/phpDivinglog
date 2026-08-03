# Tasks Document

- [x] 1. Relax database validation for SQLite DSNs in src/Support/Config.php
  - File: src/Support/Config.php
  - Modify `validateRequiredDatabase()` (private static method, ~line 401) to accept a
    `DB_DSN` that starts with `sqlite:` without requiring `DB_USER` to be set
  - Leave the existing DSN+user and host+name+user branches untouched
  - Purpose: Let a SQLite deployment configure only `DB_DSN` in `.env`, with no database
    username/password required
  - _Leverage: existing `validateRequiredDatabase()` structure and `ConfigException`_
  - _Requirements: 1.2_
  - _Prompt: Implement the task for spec sqlite-database-support, first run
    spec-workflow-guide to get the workflow guide then implement the task: Role: PHP backend
    developer working on configuration validation | Task: In
    src/Support/Config.php::validateRequiredDatabase(), add a branch that returns early when
    trim($values['DB_DSN']) is non-empty and starts with 'sqlite:' (case-insensitive), so
    DB_USER is not required for SQLite DSNs, per requirement 1.2 in requirements.md |
    Restrictions: do not change the method's signature or the existing mysql/host-based
    validation branches, keep the same ConfigException message for the failure case,
    strict_types stays on | Success: composer stan passes; a Config built from
    ['DB_DSN' => 'sqlite:/tmp/x.sqlite'] (no DB_USER) no longer throws ConfigException. Set
    the task to [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 2. Add Config unit tests for SQLite DSN validation in tests/Support/ConfigTest.php
  - File: tests/Support/ConfigTest.php
  - Add a test asserting `DB_DSN=sqlite:...` with empty `DB_USER`/`DB_PASSWORD` builds a
    valid `Config` and `dsn()` returns the DSN unchanged
  - Add a test asserting an empty `DB_DSN` with no host/name/user still throws
    `ConfigException` (regression guard for the untouched branch)
  - Purpose: Lock in the Task 1 behavior change with automated coverage
  - _Leverage: existing `testAcceptsDsnStyleDatabaseSettings`/`testAcceptsHostStyleDatabaseSettings` patterns in this file_
  - _Requirements: 1.2, 4.4_
  - _Prompt: Implement the task for spec sqlite-database-support, first run
    spec-workflow-guide to get the workflow guide then implement the task: Role: PHP QA
    engineer specializing in PHPUnit | Task: In tests/Support/ConfigTest.php, add test
    methods covering requirement 1.2/4.4: (a) Config::fromArray with DB_DSN starting
    'sqlite:' and no DB_USER/DB_PASSWORD does not throw and dsn() returns the given DSN
    verbatim; (b) empty DB_DSN with no DB_HOST/DB_NAME/DB_USER still throws ConfigException,
    following the existing test naming/assertion style in this file | Restrictions: do not
    modify existing passing tests, use self::assert* static calls matching file convention,
    no new test doubles/mocks needed | Success: composer test passes including the two new
    cases; composer stan clean. Set the task to [-] then [x] in tasks.md and call
    log-implementation with artifacts._

- [x] 3. Add SQLite branch to src/Database/Connection.php
  - File: src/Database/Connection.php
  - In `Connection::fromConfig()`, before constructing `PDO`, detect a `sqlite:` DSN
    (case-insensitive `str_starts_with`, mirroring the existing `mysql:` check at line 29)
  - For a non-`:memory:`/non-`file::memory:` SQLite DSN, extract the file path (substring
    after `sqlite:`) and throw `RuntimeException` naming the path if `!is_readable($path)`,
    before calling `new PDO(...)`
  - When the DSN is `sqlite:`, pass `PDO::SQLITE_ATTR_OPEN_FLAGS => PDO::SQLITE_OPEN_READONLY`
    in the PDO constructor's options array (only for sqlite DSNs — do not pass it for mysql)
  - Purpose: Make SQLite connections fail fast with an actionable error and open read-only,
    matching the app's existing read-only data-access contract
  - _Leverage: existing `mysql:` branch and try/catch → `RuntimeException` wrapper in this file_
  - _Requirements: 1.1, 1.3, 1.4, 1.6_
  - _Prompt: Implement the task for spec sqlite-database-support, first run
    spec-workflow-guide to get the workflow guide then implement the task: Role: PHP backend
    developer working on database connection bootstrapping | Task: In
    src/Database/Connection.php::fromConfig(), add SQLite DSN handling per requirements
    1.1/1.3/1.4/1.6 in requirements.md and the Connection component design in design.md: (1)
    detect sqlite: DSNs case-insensitively; (2) for a file-backed sqlite DSN (not
    :memory:/file::memory:), pre-check is_readable() on the extracted path and throw
    RuntimeException naming the path before constructing PDO; (3) pass
    PDO::SQLITE_ATTR_OPEN_FLAGS => PDO::SQLITE_OPEN_READONLY in the options array only when
    the DSN is sqlite; (4) leave the existing mysql: SET NAMES branch and the outer
    PDOException-to-RuntimeException wrapper unchanged | Restrictions: do not alter the
    public fromConfig(Config $config): PDO signature, do not weaken the existing mysql path,
    keep strict_types, no new dependencies | Success: composer stan passes; a sqlite DSN
    pointing at a real file connects read-only; a sqlite DSN pointing at a missing file throws
    RuntimeException whose message contains the path; mysql DSNs behave exactly as before. Set
    the task to [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 4. Add Connection unit tests for SQLite handling in tests/Database/ConnectionTest.php
  - File: tests/Database/ConnectionTest.php
  - Add a test connecting to a temp file-backed SQLite DB via `Connection::fromConfig()` and
    asserting the returned `PDO`'s driver name is `sqlite` and a simple query succeeds
  - Add a test asserting a `sqlite:` DSN pointing at a nonexistent path throws
    `RuntimeException` with the path in the message
  - Purpose: Lock in Task 3's behavior with automated coverage
  - _Leverage: existing `ConnectionTest` structure and `Config::fromArray()` usage_
  - _Requirements: 1.1, 1.4, 1.6, 4.3_
  - _Prompt: Implement the task for spec sqlite-database-support, first run
    spec-workflow-guide to get the workflow guide then implement the task: Role: PHP QA
    engineer specializing in PHPUnit | Task: In tests/Database/ConnectionTest.php, add tests
    per requirements 1.1/1.4/1.6/4.3: build a Config with DB_DSN pointing at a real temp
    SQLite file (created in the test, cleaned up in teardown or via a temp-dir helper), call
    Connection::fromConfig(), assert PDO::ATTR_DRIVER_NAME is 'sqlite' and a trivial SELECT
    works; separately, build a Config with DB_DSN pointing at a path that does not exist and
    assert Connection::fromConfig() throws RuntimeException whose getMessage() contains that
    path. Skip both tests with markTestSkipped if 'sqlite' is not in
    PDO::getAvailableDrivers(), matching the skip pattern used elsewhere in tests/Repository/
    | Restrictions: do not leave stray temp files after the test run, do not depend on test
    execution order | Success: composer test passes including the two new cases. Set the task
    to [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 5. Fix boolean-column classification in src/Repository/DiveStatisticsRepository.php
  - File: src/Repository/DiveStatisticsRepository.php
  - Add private methods `countBooleanTrue(string $column): ?int` (selects the raw column from
    `%sLogbook`, catches missing-column `PDOException` via existing `isMissingColumn()` and
    returns `null`, otherwise counts truthy rows in PHP) and
    `isTruthyBooleanValue(mixed $value): bool` (true for `'true'`/`'1'` case-insensitively
    after trim, false for everything else including `null`)
  - In `computeClassifications()`, replace the `"Deco = 'True'"`, `"Rep = 'True'"`,
    `"(DblTank = 'False' OR DblTank = 'false')"`, and `"DblTank = 'True'"` `countWhere()`
    calls with `countBooleanTrue('Deco')`, `countBooleanTrue('Rep')`, and
    `countBooleanTrue('DblTank')`, deriving `single` as `$total - twin` the same way `nodeco`/
    `norep` are already derived from `$total - deco`/`$total - rep`
  - Purpose: Correctly classify `Deco`/`Rep`/`DblTank` whether the underlying value is the
    MySQL export's `'True'`/`'False'` text or the native SQLite export's `1`/`0` integers,
    without any cross-engine SQL literal comparison
  - _Leverage: existing `isMissingColumn()`, `countWhere()` (kept for all non-boolean
    classifications), and the `nodeco`/`norep` `$total - count` derivation pattern already in
    this file_
  - _Requirements: 2.2_
  - _Prompt: Implement the task for spec sqlite-database-support, first run
    spec-workflow-guide to get the workflow guide then implement the task: Role: PHP database
    engineer | Task: In src/Repository/DiveStatisticsRepository.php, implement
    countBooleanTrue() and isTruthyBooleanValue() exactly as specified in the
    DiveStatisticsRepository component of design.md, and rewire computeClassifications() to
    use them for deco/rep/twin/single instead of the literal 'True'/'False' SQL string
    comparisons, per requirement 2.2 in requirements.md. Do NOT introduce any SQL predicate
    that OR's a string literal against an integer literal on the same column (this causes
    silent MySQL implicit-coercion miscounts, as explained in design.md's Components section)
    | Restrictions: countWhere() and all its other call sites (Entry, Water, SupplyType,
    Divetype classifications) must remain unchanged; preserve the existing missing-column ->
    null behavior exactly; strict_types stays on | Success: composer stan passes; existing
    DiveStatisticsRepositoryTest assertions for deco/nodeco/rep/norep/single/twin still pass
    unchanged (fixture uses 'True'/'False' strings); a new test (Task 6) proves integer 1/0
    values classify identically. Set the task to [-] then [x] in tasks.md and call
    log-implementation with artifacts._

- [x] 6. Add boolean-normalization unit tests in tests/Repository/DiveStatisticsRepositoryTest.php
  - File: tests/Repository/DiveStatisticsRepositoryTest.php
  - Add a test that builds an in-memory SQLite `DL_Logbook` with `Deco`/`Rep`/`DblTank` stored
    as `INTEGER` `1`/`0` (mirroring the native export's shape) and asserts
    `DiveStatisticsRepository::compute()->classifications` produces the same
    deco/nodeco/rep/norep/single/twin counts as the equivalent string-valued fixture
  - Purpose: Prove Task 5's fix works for both value representations, following the existing
    `DiveStatisticsMariaCompatibilityTest`-style pattern of a small inline schema
  - _Leverage: `DiveStatisticsMariaCompatibilityTest.php`'s inline `sqlite::memory:` schema
    pattern for the test structure_
  - _Requirements: 2.2, 4.2_
  - _Prompt: Implement the task for spec sqlite-database-support, first run
    spec-workflow-guide to get the workflow guide then implement the task: Role: PHP QA
    engineer specializing in PHPUnit | Task: In tests/Repository/DiveStatisticsRepositoryTest.php,
    add a test method that creates an in-memory sqlite::memory: PDO, creates a DL_Logbook
    table with Deco/Rep/DblTank as INTEGER, inserts a small set of rows with 1/0 values
    covering both true and false cases (plus one NULL for a boolean column), runs
    DiveStatisticsRepository::compute(), and asserts the classifications match hand-computed
    expected counts, per requirements 2.2 and 4.2 | Restrictions: mirror the
    markTestSkipped('pdo_sqlite driver is not available...') guard used in every other test in
    this file; keep the test self-contained (no shared fixture files) like
    DiveStatisticsMariaCompatibilityTest | Success: composer test passes including the new
    test; the test fails if Task 5's OR-literal pitfall were reintroduced. Set the task to
    [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 7. Add native Diving Log SQLite export fixtures
  - Files: tests/fixtures/native-schema.sql, tests/fixtures/native-seed.sql
  - Model `native-schema.sql` on the real Diving Log SQLite export's unprefixed table/column
    shape confirmed during design (`Logbook`, `Place`, `Country`, `City`, `Shop`, `Trip`,
    `Equipment`, `Buddy`, `Pictures`, `Tank`, `Userdefined`, `Personal`, `DBInfo`, `Brevets`),
    including `Logbook.ID` as `INTEGER PRIMARY KEY` alongside a plain `Number` column, and
    `Deco`/`Rep`/`DblTank`/`Entry`/`Water`/`SupplyType` as `INTEGER`
  - Populate `native-seed.sql` with small synthetic sample data only (do not copy real user
    data from `divinglog-sqlite.sql`) covering enough variety to exercise every
    classification bucket, similar in spirit to the existing `tests/fixtures/seed.sql`
  - Purpose: Give the integration/smoke tests in Tasks 8-9 a committed, synthetic fixture
    shaped like the native export
  - _Leverage: `tests/fixtures/schema.sql`/`seed.sql` as the structural template; the sampled
    real schema documented in design.md's Data Models section_
  - _Requirements: 4.1_
  - _Prompt: Implement the task for spec sqlite-database-support, first run
    spec-workflow-guide to get the workflow guide then implement the task: Role: PHP database
    engineer | Task: Create tests/fixtures/native-schema.sql and
    tests/fixtures/native-seed.sql representing the native (unprefixed) Diving Log SQLite
    export shape described in design.md's Data Models section (Shape B), with synthetic-only
    sample data covering shore/boat entries, night/drift/deep/cave/wreck/photo dive types,
    salt/fresh/brackish water, deco/no-deco, rep/non-rep, single/twin tank, and
    oc/scr/ccr supply types, per requirement 4.1 | Restrictions: table/column names must match
    the native export exactly (no DL_ prefix baked into the fixture — prefix is applied at
    query time via TABLE_PREFIX=''), do not include any real personal data from
    divinglog-sqlite.sql, keep the row count small (similar scale to the existing 3-dive
    seed.sql) | Success: both files load cleanly via $pdo->exec() against sqlite::memory: with
    no syntax errors, verified in Task 8's test. Set the task to [-] then [x] in tasks.md and
    call log-implementation with artifacts._

- [x] 8. Add native-schema repository integration test
  - File: tests/Repository/NativeSchemaCompatibilityTest.php
  - Load `native-schema.sql`/`native-seed.sql` (Task 7) into an in-memory SQLite PDO, then
    exercise `DiveRepository`, `DiveStatisticsRepository`, `DiveSiteRepository`,
    `TripRepository`, `ShopRepository`, and `EquipmentRepository` — all constructed with an
    empty table prefix (`''`) — asserting dive listing/detail, statistics classification
    counts, and place/trip/shop/equipment lookups return correct results
  - Purpose: Prove the repository layer works end-to-end against the native export shape, not
    just the classification fix in isolation
  - _Leverage: existing `DiveRepositoryTest.php`/`EntityDiveListingTest.php` patterns for
    repository construction and assertions_
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 4.2_
  - _Prompt: Implement the task for spec sqlite-database-support, first run
    spec-workflow-guide to get the workflow guide then implement the task: Role: PHP QA
    engineer specializing in integration testing | Task: Create
    tests/Repository/NativeSchemaCompatibilityTest.php that loads the Task 7 fixtures into a
    sqlite::memory: PDO and instantiates DiveRepository/DiveStatisticsRepository/
    DiveSiteRepository/TripRepository/ShopRepository/EquipmentRepository with tablePrefix=''
    (empty string), then asserts: dive listing/detail resolve correctly using the Number
    column, statistics classifications match the seed data, and place/trip/shop/equipment
    list+lookup methods return the seeded rows, covering requirements 2.1-2.5 and 4.2 in
    requirements.md | Restrictions: follow the markTestSkipped pdo_sqlite guard convention
    used throughout tests/Repository/; do not modify the fixtures from Task 7 to make
    assertions pass — fix the fixture or the test, not both to paper over a real gap;
    strict_types on | Success: composer test passes; the new test file provides equivalent
    coverage depth to DiveRepositoryTest.php/DiveStatisticsRepositoryTest.php but against the
    native schema shape. Set the task to [-] then [x] in tasks.md and call log-implementation
    with artifacts._

- [x] 9. Add native-schema HTTP smoke test
  - File: tests/Http/NativeSchemaSmokeTest.php
  - Following `WebSmokeTest`'s `seedFixtureDatabase()`/`request()` pattern, seed a dedicated
    SQLite file from the Task 7 native fixtures, set `TABLE_PREFIX=''` in the request env, and
    assert the home page, a dive detail page, and the stats page each return HTTP 200 with no
    fatal errors
  - Purpose: Catch integration-level breakage (bootstrap wiring, controllers, templates) when
    running against the native schema shape end-to-end through `public/index.php`
  - _Leverage: `WebSmokeTest.php`'s `seedFixtureDatabase()`/`request()` methods as the
    structural template (a focused subset, not full parity with `WebSmokeTest`'s ~40 cases)_
  - _Requirements: 2.1, 2.2, 4.2_
  - _Prompt: Implement the task for spec sqlite-database-support, first run
    spec-workflow-guide to get the workflow guide then implement the task: Role: PHP QA
    engineer specializing in HTTP smoke testing | Task: Create
    tests/Http/NativeSchemaSmokeTest.php modeled on WebSmokeTest.php's setUp/
    seedFixtureDatabase/request/tearDown structure, but seeding a separate SQLite file (e.g.
    tests/fixtures/native-smoke.sqlite, cleaned up each run) from tests/fixtures/
    native-schema.sql + native-seed.sql (Task 7), and setting TABLE_PREFIX='' plus the matching
    DB_DSN in the request() env overrides. Cover home page, one dive detail page, and the
    stats page, asserting HTTP 200 and absence of PHP fatal/warning markers in the body, per
    requirements 2.1/2.2/4.2 | Restrictions: keep this test focused (3-6 cases, not a full
    re-implementation of WebSmokeTest's ~40 cases); mirror the pdo_sqlite
    markTestSkipped guard; do not leave the generated .sqlite fixture file committed/dirty
    after a run | Success: composer test passes including this new file; deleting Task 5's
    fix causes this test to fail (verified manually before finalizing). Set the task to [-]
    then [x] in tasks.md and call log-implementation with artifacts._

- [x] 10. Document SQLite as a supported database option
  - Files: README.md, INSTALL.md, docs/deployment.md, .env.example
  - Add a "Database: MySQL or SQLite" subsection to each of `README.md`, `INSTALL.md`, and
    `docs/deployment.md` covering: `pdo_sqlite` as an alternative extension requirement, a
    `DB_DSN=sqlite:/absolute/path/to/divinglog.sqlite` example with a note to store the file
    outside `public/` (e.g. under `var/`), and the two supported shapes (native export with
    `TABLE_PREFIX=` empty vs. MySQL-export-shaped SQLite with `TABLE_PREFIX=DL_`)
  - Add a commented `DB_DSN=sqlite:/absolute/path/to/divinglog.sqlite` example line in
    `.env.example` next to the existing `mysql:` examples, and note that `TABLE_PREFIX=` can
    be left empty for a native export
  - Call out the known limitation that BLOB-embedded photo/scan columns in the native export
    are not rendered (the app only reads `*PhotoPath`-style filesystem-path columns)
  - Purpose: Make the SQLite path self-serve discoverable per Requirement 3
  - _Leverage: existing "Runtime configuration" section structure in README.md and the
    existing `DB_DSN` example blocks in INSTALL.md/docs/deployment.md_
  - _Requirements: 3.1, 3.2, 3.3, 3.4_
  - _Prompt: Implement the task for spec sqlite-database-support, first run
    spec-workflow-guide to get the workflow guide then implement the task: Role: Technical
    writer with PHP/ops background | Task: Update README.md, INSTALL.md, docs/deployment.md,
    and .env.example to document SQLite as a supported DB_DSN option per requirements
    3.1-3.4 in requirements.md: extension requirement, DSN example, the two supported schema
    shapes and how TABLE_PREFIX selects between them, and the BLOB-photo known limitation from
    requirements.md's Non-Goals (2) | Restrictions: match each file's existing tone/format
    (README is high-level, INSTALL.md is a checklist, docs/deployment.md is deployment-detail
    focused); do not remove or restructure existing MySQL documentation, only add alongside
    it | Success: a reader with no prior context can follow README/INSTALL/deployment docs to
    configure DB_DSN for either a native or MySQL-shaped SQLite file without reading source
    code. Set the task to [-] then [x] in tasks.md and call log-implementation with
    artifacts._

- [x] 11. Run full quality gate
  - No new files — verification task
  - Run `composer test && composer stan && composer cs` and fix any failures surfaced by
    Tasks 1-10
  - Purpose: Confirm the feature is complete and the codebase is in a clean, mergeable state
  - _Leverage: existing composer scripts (`test`, `stan`, `cs`)_
  - _Requirements: 4.5_
  - _Prompt: Implement the task for spec sqlite-database-support, first run
    spec-workflow-guide to get the workflow guide then implement the task: Role: PHP developer
    performing final verification | Task: Run composer test && composer stan && composer cs
    per requirement 4.5 in requirements.md, and fix any failures introduced by Tasks 1-10
    (test regressions, PHPStan errors, PSR-12 violations) without changing the intended
    behavior established by those tasks | Restrictions: do not weaken PHPStan rules or
    suppress errors with baseline/ignore entries to force a pass; do not skip or delete
    failing tests to make the gate green | Success: composer test && composer stan && composer
    cs exits 0. Set the task to [-] then [x] in tasks.md and call log-implementation with
    artifacts._
