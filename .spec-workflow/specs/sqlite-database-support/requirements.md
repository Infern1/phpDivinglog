# Requirements Document

## Introduction

phpDivingLog currently requires a MySQL server: the Diving Log desktop export must be
imported into MySQL before the app can display anything. Many divers who just want a
simple personal logbook site do not want to provision and maintain a MySQL server — Diving
Log itself can export the logbook directly as a **SQLite** file (as confirmed against a real
export: `Logbook`, `Place`, `Country`, `City`, `Shop`, `Trip`, `Equipment`, `Buddy`,
`Pictures`, `Tank`, `Userdefined`, `Personal`, `DBInfo`, `Brevets`, `Fish`/`FishRel`,
`Signature`, `Bookmarks` — unprefixed table names, SQLite 3 format).

This feature adds SQLite as a first-class, officially supported alternative data source
to MySQL, so a diver can point `DB_DSN` at a `.sqlite` file — either their native Diving Log
SQLite export or a copy shaped like the existing `TABLE_PREFIX`-based MySQL export — and get
a fully working site with zero database server to install or maintain.

The PDO data-access layer is already largely engine-agnostic (`Connection::fromConfig`
already branches on DSN scheme, and the test suite already runs full repository/HTTP smoke
coverage against SQLite fixtures per `tests/fixtures/`). This spec closes the remaining gaps
between "SQLite works in tests" and "SQLite is a supported, documented production deployment
option," including the concrete schema/value differences between the native Diving Log
SQLite export and the MySQL-export schema the repositories were originally written against
(e.g. `Deco`/`Rep`/`DblTank` stored as the string `'True'`/`'False'` in the MySQL export vs.
integer `1`/`0` in the native SQLite export).

## Alignment with Product Vision

This directly advances several `product.md` principles and goals:

- **Host-friendliness** (product principle): SQLite needs no database server at all, which is
  a strictly easier deployment than "modest LAMP/LEMP hosting" — it lowers the bar further,
  including to non-technical shared hosting with no MySQL access, or a single static-ish
  file-based deploy.
- **Ease-of-install success metric** (`product.md`): "A diver can go from a fresh MySQL dump
  to a working site by editing a single config file" extends naturally to "...or from a fresh
  SQLite export."
- **Faithful to the source data / schema-agnostic repositories** (`product.md`, `tech.md`):
  Repositories already resolve optional columns defensively; this feature extends that
  defensiveness to cover the native SQLite export's column/value shape, not just the MySQL
  export's shape.
- **Read-only presentation** (product principle): SQLite access is read-only, consistent with
  the app's existing read-only contract — no new write paths are introduced.
- **Config over code** (product principle): Switching database engines SHALL remain a `.env`
  change (`DB_DSN`), not a code change.

## Requirements

### Requirement 1 — SQLite as a supported `DB_DSN` value

**User Story:** As a self-hoster without a MySQL server, I want to set `DB_DSN` to a SQLite
file path, so that the app runs entirely against a local SQLite database file.

#### Acceptance Criteria

1. WHEN `DB_DSN` starts with `sqlite:` THEN the system SHALL connect using the PDO SQLite
   driver instead of assuming MySQL.
2. WHEN `DB_DSN` is a SQLite DSN THEN the system SHALL NOT require `DB_USER`/`DB_PASSWORD` to
   be set to non-empty values (SQLite has no server-side authentication).
3. WHEN `DB_DSN` is a SQLite DSN THEN the system SHALL NOT execute the MySQL-only
   `SET NAMES utf8mb4` bootstrap statement currently gated in `Connection::fromConfig`.
4. WHEN a SQLite DSN points at a file that does not exist or is not readable THEN the system
   SHALL surface a clear, actionable error (not a raw `PDOException` stack trace) identifying
   the configured path.
5. IF `DB_DSN` is left empty (host/port/name style config) THEN the system SHALL continue to
   build a `mysql:` DSN exactly as it does today, preserving full backward compatibility for
   existing MySQL installs.
6. WHEN the app connects via a SQLite DSN THEN the system SHALL open the database in a mode
   that does not require write access to the file or its containing directory beyond what
   SQLite itself needs for its own temp/journal files, consistent with the app's read-only
   data-access contract.

### Requirement 2 — Native Diving Log SQLite export compatibility

**User Story:** As a diver, I want to point the app directly at the `.sqlite` file exported
by the Diving Log desktop program, so that I don't have to run any conversion or import step.

#### Acceptance Criteria

1. WHEN `TABLE_PREFIX` is set to an empty string THEN the system SHALL query unprefixed table
   names (`Logbook`, `Place`, `Country`, `City`, `Shop`, `Trip`, `Equipment`, `Buddy`,
   `Pictures`, `Tank`, `Userdefined`, `Personal`, `DBInfo`, `Brevets`), matching the native
   Diving Log SQLite export's table naming.
2. WHEN repositories evaluate boolean-style logbook columns (`Deco`, `Rep`, `DblTank`) THEN
   the system SHALL correctly classify dives whether the underlying value is the MySQL
   export's string form (`'True'`/`'False'`) or the native SQLite export's integer form
   (`1`/`0`), for both `DiveStatisticsRepository` and any other repository code with the same
   assumption.
3. WHEN the `Logbook` table's primary identifier is `ID` (native export) rather than `Number`
   / `LogID` (MySQL export) THEN repositories SHALL continue to resolve dive identity
   correctly, extending the existing `LogID ?? ID ?? Number` fallback pattern where such
   fallback is not already present.
4. WHEN reading location/trip/shop/equipment relations from the native export's ID columns
   (`PlaceID`, `CountryID`, `CityID`, `ShopID`, `TripID`, `EquipmentID`) THEN the system SHALL
   resolve them consistently with how it resolves the equivalent MySQL-export columns today.
5. IF a table or column present in the MySQL-export schema is absent from the native SQLite
   export (or vice versa) THEN the system SHALL degrade gracefully (omit the dependent
   feature/section) rather than error, consistent with the existing "missing column" handling
   pattern (`isMissingColumn()`).

### Requirement 3 — Documentation and setup guidance

**User Story:** As a new user without prior MySQL experience, I want clear setup docs for the
SQLite path, so that I can deploy the app without reading source code.

#### Acceptance Criteria

1. WHEN a user reads `README.md` / `INSTALL.md` / `docs/deployment.md` THEN the system SHALL
   document SQLite as a supported database option alongside MySQL, including example
   `DB_DSN` values and the `pdo_sqlite` extension requirement.
2. WHEN a user reads the SQLite setup docs THEN the system SHALL explain the two supported
   SQLite shapes: (a) a native, unprefixed Diving Log SQLite export with `TABLE_PREFIX=`
   (empty), and (b) a MySQL-export-shaped SQLite file with the usual `TABLE_PREFIX`.
3. WHEN a user reads the SQLite setup docs THEN the system SHALL call out known limitations
   (see Requirement 5) so expectations are set up front.
4. WHEN `.env.example` is read THEN the system SHALL include a commented SQLite `DB_DSN`
   example (e.g. `sqlite:/absolute/path/to/divinglog.sqlite`) next to the existing MySQL
   examples.

### Requirement 4 — Automated test coverage for the SQLite path

**User Story:** As a maintainer, I want automated tests that exercise the native Diving Log
SQLite schema shape (not just the existing MySQL-export-shaped fixture), so that regressions
in SQLite compatibility are caught in CI.

#### Acceptance Criteria

1. WHEN the test suite runs THEN the system SHALL include a fixture schema/seed modeled on
   the native (unprefixed) Diving Log SQLite export's table/column shape, distinct from the
   existing `DL_`-prefixed `tests/fixtures/schema.sql`.
2. WHEN repository integration tests run against the native-shape fixture THEN the system
   SHALL assert that dive listing, dive detail, statistics classification (deco/rep/twin
   counts), and location/trip/shop/equipment lookups produce correct results.
3. WHEN `Connection::fromConfig` is unit-tested THEN the system SHALL assert SQLite DSNs skip
   the MySQL-only `SET NAMES` statement and MySQL DSNs are unaffected.
4. WHEN `Config` validation is unit-tested THEN the system SHALL assert a SQLite `DB_DSN`
   without `DB_USER`/`DB_PASSWORD` passes validation.
5. WHEN `composer test && composer stan && composer cs` runs THEN it SHALL pass with the new
   code and fixtures included.

### Requirement 5 — Explicit non-goals for this iteration

**User Story:** As a maintainer, I want the scope of SQLite support bounded, so that this
feature ships as a focused, reviewable increment rather than an open-ended rewrite.

#### Acceptance Criteria

1. THEN the system SHALL NOT implement a MySQL-to-SQLite or SQLite-to-MySQL data migration/
   import tool as part of this feature — users bring their own SQLite file (native export or
   otherwise).
2. THEN the system SHALL NOT implement rendering of images stored as in-database BLOBs
   (`Photo`, `Picture`, `Map`, `Scan1`/`Scan2`, `MediCheck`, `Stamp` columns present in the
   native SQLite export). The app's existing photo/media features continue to rely on
   filesystem paths (`*PhotoPath` style columns already used by the MySQL-export schema); if
   only BLOB data is present, the affected image SHALL simply not render — this SHALL be
   called out as a known limitation, not silently fixed here.
3. THEN the system SHALL NOT add write/import/sync capability to the SQLite file — access
   remains strictly read-only, matching the app's existing MySQL read-only contract.
4. THEN the system SHALL NOT add support for concurrent multi-process write access tuning
   (e.g. WAL mode configuration) since the app performs no writes.

## Non-Functional Requirements

### Code Architecture and Modularity
- **Single Responsibility Principle**: SQLite-specific branching stays isolated to
  `Connection`/`Config` (connection/DSN concerns) and repository-level value-normalization
  helpers (e.g. a shared boolean-column predicate helper) — no engine-specific `if` branches
  should leak into controllers or templates.
- **Modular Design**: The native-export table/column mapping should be expressed as data
  (column fallback lists) consistent with the existing `firstNonEmptyString`/`firstNumeric`
  pattern in `DiveRepository`, not duplicated per-repository logic.
- **Dependency Management**: No new Composer dependencies are required — `pdo_sqlite` is a
  standard PHP extension already used by the test suite.
- **Clear Interfaces**: Repositories continue to depend only on `PDO` + table prefix; no
  repository should need to know which database engine is in use.

### Performance
- SQLite reads for a personal-scale logbook (hundreds to low thousands of dives, matching the
  real export sampled during design: 214 dives, 127 places, 43 buddies, 225 pictures) SHALL
  perform comparably to the existing MySQL path for the same page loads — no N+1 query
  regressions introduced by engine-detection branching.

### Security
- SQLite file paths configured via `DB_DSN` SHALL be treated as trusted deployment
  configuration (not user input); no new user-facing attack surface is introduced.
- Documentation SHALL recommend storing the SQLite file outside the public web root (e.g.
  under `var/`), consistent with how `var/cache` is already kept out of `public/`.

### Reliability
- A missing, unreadable, or malformed SQLite file SHALL fail fast at bootstrap with a clear
  error rather than partially rendering pages with silent data gaps.

### Usability
- Setup documentation SHALL be concrete enough that a user with no prior database experience
  can go from "I have a `.sqlite` file exported from Diving Log" to "my site is running" by
  editing only `.env`.
