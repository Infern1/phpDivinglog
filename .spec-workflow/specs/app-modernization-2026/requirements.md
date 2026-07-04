# Requirements Document

## Introduction

phpDivingLog is a PHP web application that publishes dive data exported from the
**Diving Log** desktop program. The current codebase (app version 3.2) is built on
2011-era practices: a single ~5,500-line `classes.inc.php`, the WordPress `wpdb` class
vendored as the database layer, Smarty 3.1.30 for templating, and a stack of dated
front-end libraries (jQuery, jQuery DataTables, Highslide JS, jqPlot) plus an RTF parsing
class. There is no Composer autoloading, no automated tests, and no clean separation
between data access, domain logic, and presentation.

This spec defines a **big-bang rewrite** that brings the application up to 2026 standards
by splitting the system into a framework-agnostic, PDO-based, PSR-4 core package and thin
delivery adapters on top. The rewrite drops the WordPress `wpdb` dependency (replacing it
with PDO), replaces Smarty with Twig, replaces the legacy JavaScript libraries with modern
equivalents, and replaces the RTF class with a maintained package. It targets PHP 8.3+,
runs as its own standalone service while remaining installable on standard shared hosting,
and preserves full feature parity and read-only compatibility with the existing Diving Log
MySQL schema.

The delivery architecture is **Approach B**: a decoupled core, a standalone server-rendered
web application, and a JSON API. A WordPress consumer shim is explicitly out of scope for
implementation but the API is designed so it could be added later without changing the core.

## Alignment with Product Vision

This rewrite directly advances the goals in `product.md` and the modernization direction in
`tech.md`:

- **Read-only presentation** (product principle): The new core keeps phpDivingLog a display
  layer over Diving Log data; no editing of the source logbook is introduced.
- **Config over code** (product principle): Behavior stays driven by configuration, migrated
  to a modern env-based mechanism with backward-compatible defaults.
- **Host-friendliness** (product principle): The app must still deploy on modest LAMP/LEMP
  hosting while also running as a standalone service on self-managed infrastructure.
- **Faithful to source data** (product principle): Metric storage and display-time
  conversion are preserved exactly.
- **Known limitations addressed** (tech.md): Decomposes the monolithic `classes.inc.php`,
  introduces automated tests, removes legacy/duplicated vendored code, and eliminates the
  built-in-auth and PHP-version-drift risks called out as technical debt.
- **Future vision** (product.md): Realizes the "modernization" and "embedding" enhancements
  by producing a JSON API that a WordPress (or any) frontend can consume.

## Requirements

### Requirement 1 — Framework-agnostic core package

**User Story:** As a maintainer, I want the domain and data-access logic packaged as a
plain, framework-agnostic PHP library with Composer and PSR-4 autoloading, so that the
application no longer depends on WordPress, Smarty, or any framework and can be reused by
multiple delivery adapters.

#### Acceptance Criteria

1. WHEN the project is set up THEN the system SHALL provide a `composer.json` defining a
   PSR-4 autoloaded namespace for the core (e.g. `PhpDivingLog\`) with no `require` on
   WordPress, Smarty, or any web framework.
2. WHEN core classes are loaded THEN the system SHALL rely solely on Composer autoloading
   and SHALL NOT use `require_once`/`include` of source files for autoloadable classes.
3. IF a delivery adapter (standalone, JSON API, future WP shim) is removed THEN the core
   package SHALL continue to build and pass its tests unchanged.
4. WHEN the core is inspected THEN it SHALL NOT emit HTML, read `$_SERVER`/`$_GET` directly,
   or contain presentation logic — those concerns SHALL live in adapters only.
5. WHEN targeting the runtime THEN the core SHALL declare and require PHP 8.3 or newer in
   `composer.json`.

### Requirement 2 — PDO data-access layer replacing `wpdb`

**User Story:** As a maintainer, I want all database access to go through PDO in a single
repository layer, so that the WordPress `wpdb` dependency is removed and queries are safe
and centralized.

#### Acceptance Criteria

1. WHEN the application accesses the database THEN it SHALL use PDO with its own connection
   and credentials and SHALL NOT use `wpdb` or `wp-db.php`.
2. WHEN any query includes request-derived values THEN the system SHALL use PDO prepared
   statements with bound parameters (no string-concatenated SQL).
3. WHEN a domain entity's data is needed THEN it SHALL be retrieved through a repository
   class (e.g. `DiveRepository`, `DiveSiteRepository`) that is the only place issuing SQL
   for that entity.
4. IF the configured `table_prefix` is set THEN all repository queries SHALL honor the
   prefix, preserving single-user and multi-user (prefix-based) modes.
5. WHEN the existing Diving Log MySQL schema is queried THEN the system SHALL treat it as
   read-only and SHALL NOT issue INSERT/UPDATE/DELETE/DDL against Diving Log tables.
6. WHEN a database or query error occurs THEN the system SHALL fail safely with a logged
   error and SHALL NOT leak SQL, credentials, or stack traces to the client.

### Requirement 3 — Feature parity across all views

**User Story:** As a diver publishing my logbook, I want every page the current application
provides to exist in the rewrite, so that no functionality is lost in the migration.

#### Acceptance Criteria

1. WHEN the rewrite is complete THEN the system SHALL reproduce all existing views: dive
   list and dive detail; dive site, country, city, dive shop, and trip overviews and
   details; equipment overview and detail; diving statistics; the photo gallery; and the
   embeddable dive summary.
2. WHEN a dive detail page is rendered THEN it SHALL include main details, buddies,
   conditions, breathing/gas details, equipment used, comments, user-defined fields, and
   the dive profile graph, matching the current feature set.
3. WHEN cross-links exist today (e.g. dive → site → country/city, trip → dives) THEN the
   rewrite SHALL preserve equivalent navigation between entities.
4. WHEN equipment service reminders are due THEN the system SHALL surface them as the
   current app does, driven by configuration.
5. IF a feature in the legacy app is intentionally dropped THEN it SHALL be listed explicitly
   in the design as out of scope with a rationale.

### Requirement 4 — Twig-based server-rendered presentation

**User Story:** As a maintainer, I want the HTML rendered with Twig instead of Smarty, so
that templates use a modern, maintained engine with auto-escaping and clean separation from
domain code.

#### Acceptance Criteria

1. WHEN any page is rendered THEN the system SHALL use Twig templates and SHALL NOT use
   Smarty or the `includes/smarty/` library.
2. WHEN template output includes data values THEN Twig auto-escaping SHALL be enabled so
   output is HTML-escaped by default.
3. WHEN shared layout is needed THEN templates SHALL use Twig inheritance/includes for
   header, footer, navigation, and list/table fragments instead of duplicated markup.
4. WHEN the standalone app renders a page THEN template rendering SHALL be invoked from the
   adapter layer, not from core domain classes.

### Requirement 5 — Modern front-end assets replacing legacy JS libraries

**User Story:** As a user, I want the interactive elements (sortable tables, dive profile
charts, image galleries, RTF comments) to work using modern, maintained libraries, so that
the site is fast, secure, and not reliant on abandoned dependencies.

#### Acceptance Criteria

1. WHEN interactive tables are shown THEN the system SHALL provide sorting/paging using a
   modern approach (a maintained library or native browser features) and SHALL NOT use
   jQuery or jQuery DataTables.
2. WHEN a dive profile is displayed THEN the system SHALL render it with a maintained,
   modern charting library and SHALL NOT use jqPlot.
3. WHEN dive/equipment/map/certification images are viewed THEN the system SHALL provide an
   overlay/lightbox using a modern library or native `<dialog>`/CSS and SHALL NOT use
   Highslide JS.
4. WHEN RTF-formatted comments are present THEN the system SHALL convert them to safe HTML
   using a maintained package (or a purpose-built, tested converter) and SHALL NOT use the
   legacy `RTFClass`.
5. WHEN front-end JavaScript is delivered THEN it SHALL not depend on jQuery; interactivity
   SHALL use vanilla JS / web components.
6. WHEN converted RTF/comment content is rendered THEN it SHALL be sanitized so it cannot
   inject active content (XSS-safe).

### Requirement 6 — JSON API for external consumers

**User Story:** As an integrator, I want a JSON API that exposes the same logbook data, so
that external frontends (including a future WordPress shortcode) can consume phpDivingLog
without sharing its code or runtime.

#### Acceptance Criteria

1. WHEN the JSON API is called for a resource collection or item (dives, sites, countries,
   cities, shops, trips, equipment, stats) THEN the system SHALL return the data as JSON
   with appropriate HTTP status codes and `Content-Type: application/json`.
2. WHEN the JSON API and the standalone HTML app return the same logical data THEN both
   SHALL source it from the same core repositories (no duplicated query logic).
3. WHEN an unknown or invalid resource is requested THEN the API SHALL return a structured
   JSON error with a 4xx status and SHALL NOT leak internal details.
4. WHEN the API is documented THEN the design SHALL describe the endpoint shape so a WP (or
   other) consumer shim can be built later without core changes.
5. IF the API is exposed publicly THEN it SHALL support the same read-only, table-prefix and
   multi-user scoping as the HTML app.

### Requirement 7 — Modernized, backward-considerate configuration

**User Story:** As an operator, I want configuration handled with modern practices (env/
typed config) while keeping the existing options meaningful, so that setup is simple and
secrets are not committed to the repo.

#### Acceptance Criteria

1. WHEN the app is configured THEN database credentials and environment-specific settings
   SHALL be provided via environment variables or an untracked config file, and secrets
   SHALL NOT be committed to version control.
2. WHEN existing behavioral options are needed (table prefix, unit conversions, date/coord
   formats, list sizes, file paths, feature toggles) THEN the new configuration SHALL
   provide equivalents with sensible defaults.
3. WHEN configuration is missing or invalid at startup THEN the system SHALL report a clear,
   actionable error and SHALL NOT expose sensitive values.
4. WHEN a `.env`/config example is provided THEN it SHALL document every option, mirroring
   the coverage of the current `config.inc.php.example`.

### Requirement 8 — Unit conversion, formatting, and localization preserved

**User Story:** As a diver with an international audience, I want metric/imperial conversion,
coordinate/date formatting, and UI language selection to work as before, so that the rewrite
does not regress presentation behavior.

#### Acceptance Criteria

1. WHEN a measurement is displayed THEN the system SHALL convert depth, pressure, weight,
   temperature, and volume between metric and imperial according to configuration, keeping
   metric as the stored form.
2. WHEN coordinates and dates are displayed THEN the system SHALL support the existing format
   options (e.g. `d`/`dm`/`dms` coordinates, configurable date formats, decimal separator).
3. WHEN the UI language is configured THEN the system SHALL render interface strings from a
   selectable language source, preserving the multi-language capability.
4. WHEN conversion/formatting logic is implemented THEN it SHALL live in the core (reusable
   by both the HTML app and the JSON API) rather than in templates.

### Requirement 9 — Security hardening

**User Story:** As an operator, I want the rewrite to close the security gaps of the legacy
app, so that publishing my logbook does not expose me to injection or content-based attacks.

#### Acceptance Criteria

1. WHEN request parameters drive data lookups THEN the system SHALL validate/normalize input
   and use bound parameters, preventing SQL injection.
2. WHEN any user-influenced value is rendered THEN it SHALL be output-escaped (Twig
   auto-escaping) or sanitized, preventing XSS.
3. WHEN image or file paths are derived from data THEN the system SHALL constrain them to
   the configured media directories, preventing path traversal.
4. WHEN errors occur in production mode THEN the system SHALL show a generic message to users
   and log details server-side, never exposing stack traces, SQL, or credentials.
5. WHEN the app runs THEN it SHALL document that access control (public vs private logbook)
   is enforced at the deployment layer, and SHALL NOT weaken that expectation.

### Requirement 10 — Quality tooling and automated tests

**User Story:** As a maintainer, I want automated tests and static analysis, so that the
rewrite is verifiable and future changes are protected against regressions.

#### Acceptance Criteria

1. WHEN the core is built THEN the project SHALL include a PHPUnit test suite covering the
   repositories (against a test fixture/schema) and the conversion/formatting logic.
2. WHEN code quality is checked THEN the project SHALL include static analysis (e.g. PHPStan
   or Psalm) and a PSR-12 coding-standard check, runnable via Composer scripts.
3. WHEN a repository method is tested THEN it SHALL be verified without requiring a live
   Diving Log desktop export, using seeded fixtures.
4. WHEN CI-style checks are run THEN tests, static analysis, and style checks SHALL be
   invocable with documented commands.

### Requirement 11 — Deployment as standalone service and on shared hosting

**User Story:** As an operator, I want to deploy the app either as its own service on my
infrastructure or by copying it to a standard web host, so that the modernization does not
lock me out of low-cost hosting.

#### Acceptance Criteria

1. WHEN deployed as a standalone service THEN the system SHALL run behind a single front
   controller entry point with a documented web-server configuration for Nginx and Apache.
2. WHEN deployed on shared hosting THEN the system SHALL be installable without requiring
   root or long-running processes, with Composer dependencies vendored/installable, and
   SHALL support hosts without mod_rewrite via a documented fallback.
3. WHEN a release artifact is produced THEN it SHALL include the `vendor/` dependencies (or
   a documented build step) so the target host does not require running Composer if
   unavailable.
4. WHEN writable directories are required (template/cache, thumbnails) THEN the system SHALL
   document them and fail with a clear message if they are not writable.

## Non-Functional Requirements

### Code Architecture and Modularity
- **Single Responsibility Principle**: Each class has one purpose; the monolithic
  `classes.inc.php` is replaced by focused classes (one entity per repository, one concern
  per service).
- **Layered design**: Strict separation between core (domain + data access), adapters
  (standalone HTTP, JSON API), and presentation (Twig templates). Dependencies point inward
  toward the core only.
- **Dependency Management**: Core depends only on PHP + PDO; third-party libraries are
  managed by Composer and kept minimal. No adapter leaks into the core.
- **Clear Interfaces**: Repositories and services expose typed method contracts; adapters
  consume those contracts and never issue SQL or embed domain logic.

### Performance
- Page rendering for a typical logbook SHALL be comparable to or faster than the legacy app;
  template compilation and image thumbnails SHALL be cached on disk.
- The JSON API SHALL reuse core repositories so no query is executed twice for the same
  logical request within a request lifecycle.

### Security
- All SQL via prepared statements; all output escaped by default; media paths constrained to
  configured directories; secrets sourced from environment/untracked config; production
  errors non-revealing.

### Reliability
- Missing/invalid configuration, unreadable media, and database failures SHALL degrade
  gracefully with logged, actionable errors rather than fatal, information-leaking crashes.

### Usability
- Rendered pages SHALL preserve the information architecture and navigation of the current
  app so existing users are not disoriented, while using modern, accessible HTML.

### Compatibility
- Runs on PHP 8.3+; supports MySQL via PDO with mysqlnd; reads the current Diving Log export
  schema unchanged; supports both pretty-URL (rewrite) and query-string routing fallback.
