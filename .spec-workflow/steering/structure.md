# Project Structure

## Directory Organization

```
phpDivinglog/
├── public/                   # Web root (document root points here)
│   ├── index.php             #   Front controller: Twig web app
│   ├── api.php               #   Front controller: JSON API
│   ├── assets/
│   │   ├── css/custom.css     #     Project widgets built on Beer CSS theme tokens
│   │   ├── js/                #     theme, chart-theme, profile-chart, stats-chart, tables, lightbox
│   │   └── vendor/beercss/    #     Vendored Beer CSS + Material Symbols + Inter (no CDN)
│   └── images/               #   Static + user media (pictures, thumbs, maps, flags, equipment, favicon)
│
├── src/                      # Core domain/data layer (PhpDivingLog\ namespace, PSR-4)
│   ├── Model/                #   Immutable readonly DTOs (Dive, DiveSite, Personal, Certification, Tank, ...)
│   ├── Repository/           #   Read-only PDO repositories (DiveRepository, DiveSiteRepository, ...)
│   ├── Database/             #   Connection (PDO factory + table-prefix validation)
│   └── Support/              #   Config, UnitConverter, Formatter, MediaResolver, RtfConverter, etc.
│
├── adapters/
│   ├── web/                  # Web adapter (PhpDivingLog\Adapters\Web\)
│   │   ├── bootstrap.php     #   Wires config, PDO, services, repositories into a container
│   │   ├── Router.php        #   Maps request URIs / query-string mode to route keys
│   │   ├── TwigRenderer.php  #   Renders templates/ and injects Twig globals (app_name)
│   │   └── Controller/       #   Build view-models from repositories + services
│   └── api/                  # API adapter (JSON controllers, e.g. dive profile series)
│
├── templates/                # Twig templates (*.html.twig)
│   ├── layout.html.twig      #   Base shell (head, theme bootstrap, favicon, nav/header/footer)
│   ├── partials/             #   header, nav, footer, dive_rows, table, pagination
│   └── *_overview / *_detail #   Per-concept overview + detail views
│
├── tests/                    # PHPUnit tests
│   ├── Http/WebSmokeTest.php  #   End-to-end HTTP smoke tests (require pdo_sqlite)
│   ├── Repository/ | Support/ #   Fixture-backed integration + unit tests
│   └── fixtures/             #   schema.sql, seed.sql, certs.sql (SQLite fixtures)
│
├── resources/lang/           # UI translation files
├── tools/build-assets.mjs    # Optional helper to copy/refresh vendored front-end assets
├── var/cache/twig/           # Twig compiled cache (writable, git-ignored)
├── docs/                     # Deployment/testing/API/frontend docs
├── .env.example              # Source of truth for environment configuration
├── composer.json             # PSR-4 autoloading + quality scripts (test/stan/cs)
├── phpunit.xml / phpstan.neon / phpcs config
└── CHANGELOG / README.md / AGENTS.md
```

The layout is **grouped by responsibility**: framework-agnostic core in `src/`, delivery
mechanisms in `adapters/`, the web root and assets in `public/`, views in `templates/`,
and tests in `tests/`.

## Naming Conventions

### Files
- **Front controllers**: `public/index.php` (web) and `public/api.php` (JSON API).
- **Models**: one class per file under `src/Model/` — `Dive.php`, `DiveSite.php`,
  `Certification.php`, `Tank.php`, `Personal.php`.
- **Repositories**: `src/Repository/<Concept>Repository.php` — `DiveRepository.php`,
  `DiveSiteRepository.php`, `CertificationRepository.php`.
- **Controllers**: `adapters/web/Controller/<Concept>Controller.php`.
- **Templates**: lowercase with underscores; paired overview/detail suffixes —
  `divesite_overview.html.twig`, `divesite_detail.html.twig`, `dive_detail.html.twig`.
- **Test fixtures**: SQL under `tests/fixtures/` — `schema.sql`, `seed.sql`, `certs.sql`.

### Code
- **Namespaces**: `PhpDivingLog\` (core, mapped to `src/`) and
  `PhpDivingLog\Adapters\` (mapped to `adapters/`), PSR-4.
- **Classes/Types**: `PascalCase` — `DiveRepository`, `DiveStatisticsController`,
  `MediaResolver`. Models are `final readonly` DTOs.
- **Methods/Functions**: `camelCase` — `findByNumber()`, `listOverviewByCountry()`,
  `waterTypesByPlace()`.
- **Environment keys**: `UPPER_SNAKE_CASE` in `.env` (`TABLE_PREFIX`, `APP_QUERY_STRING`,
  `APP_USER_SHOW_CERTS`, `APP_USER_PATH_WEB`).
- **Twig variables**: `snake_case` view-model keys (`max_depth_display`, `certification_rows`).

## Import Patterns

### Bootstrapping
1. A front controller (`public/index.php` / `public/api.php`) requires
   `adapters/web/bootstrap.php`.
2. `bootstrap.php` loads `.env` via `Config`, builds the PDO `Connection`, validates the
   table prefix, and constructs the support services + repository set into a container array.
3. The front controller resolves the route (`Router`), instantiates the relevant controller
   with its dependencies, and renders the matching Twig template (or emits JSON).

### Module/Include Organization
- **Composer PSR-4 autoloading** — no manual `require_once` for classes.
- **Configuration** comes from environment variables (`.env`, defaults in `Config`).
- **Web-facing media paths** come from `APP_*_PATH_WEB` env keys, resolved by `MediaResolver`.

## Code Structure Patterns

### Request Flow (web)
1. Front controller resolves the request URI (and query-string mode) via `Router`.
2. It selects a controller and calls the relevant action (`overview()` / `detail($id)`).
3. The controller pulls data from repositories, applies support services (unit conversion,
   formatting, media resolution), and returns a view-model array.
4. `TwigRenderer` renders the matching template with that view-model and injected globals.

### Domain/Data Organization
- **Repositories** own all SQL via prepared PDO statements and are **read-only** against the
  Diving Log schema. They are defensive/schema-agnostic where the export varies (optional
  columns via `SELECT *` + aliases, alternate country→site mapping, inline vs. table tank
  data, `Brevets` certifications).
- **Models** are immutable readonly DTOs returned by repositories.
- **Controllers** contain no SQL and emit no HTML; they orchestrate repositories + services
  into view-models.

### Template Organization (in `templates/`)
- Overview vs. detail split per concept (`*_overview.html.twig` / `*_detail.html.twig`).
- Shared chrome and fragments in `partials/` (`header`, `nav`, `footer`, `dive_rows`,
  `table`, `pagination`), extended from `layout.html.twig`.

## Code Organization Principles
1. **Separation by responsibility**: core domain/data (`src/`), delivery (`adapters/`),
   views (`templates/`), and web root/assets (`public/`) are distinct.
2. **Config-driven behavior**: units, paths, and feature toggles live in `.env`, not code.
3. **Consistency across views**: every controller uses the same request→repository→view-model
   →template flow, so new views mirror the existing ones.
4. **Faithful, read-only data handling**: metric values stay metric internally; conversion
   and formatting happen at the controller/template boundary; the Diving Log schema is never
   written to.

## Module Boundaries
- **Adapters vs. core**: `adapters/` may depend on `src/`, never the reverse. Core has no
  knowledge of HTTP, Twig, or JSON.
- **Controllers vs. repositories**: controllers orchestrate; repositories own SQL.
- **Logic vs. presentation**: controllers must not emit HTML — rendering belongs to Twig.
- **App code vs. vendored assets**: front-end libraries live under
  `public/assets/vendor/` and are treated as external (pinned, documented in a VERSIONS file).
- **Config vs. code**: user settings live only in `.env` (copied from `.env.example`).

## Code Size Guidelines
- **Repositories/controllers**: keep methods focused; prefer small helpers over large
  multi-purpose functions.
- **Front controllers**: keep thin — routing and rendering only.
- **Templates**: keep per-view templates focused; factor shared markup into partials.

## Documentation Standards
- Public APIs and non-trivial logic carry PHPDoc/type annotations (checked by PHPStan).
- Configuration options are documented in `.env.example` (the source of truth).
- User-facing setup lives in `README.md` / `docs/`; agent/contributor guidance in
  `AGENTS.md`; release history in `CHANGELOG`.
- Verification commands: `composer test`, `composer stan`, `composer cs`.
