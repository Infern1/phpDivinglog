# Technology Stack

## Project Type
Server-rendered PHP web application with a layered, modern architecture. It is a
presentation/reporting layer over a MySQL database that has been populated by exporting data
from the **Diving Log** desktop program. It ships two front controllers: a Twig-rendered web
app and a JSON API. Front-end assets are vendored locally and served from `public/`.

## Core Technologies

### Primary Language(s)
- **Language**: PHP >= 8.3 (see `composer.json`).
- **Runtime**: PHP running under a web server (Apache or Nginx) via PHP-FPM/mod_php.
- **Language-specific tools**: Composer for PSR-4 autoloading and dev tooling.

### Key Dependencies/Libraries
- **twig/twig ^3.12**: Template engine for the web adapter's HTML rendering.
- **Beer CSS (Material Design 3)**: Vendored under `public/assets/vendor/beercss/`
  (`beer.min.css`, `beer.min.js`, `material-dynamic-colors.min.js`), plus Material Symbols
  and Inter — served locally with no runtime CDN dependency.
- **Project JS** (`public/assets/js/`): theme controller (light/dark + palettes),
  chart-theme helper, dive profile chart, stats chart, sortable/clickable tables, and a
  lightbox — all dependency-free vanilla JS.
- **Dev tools**: PHPUnit ^11.2, PHPStan ^1.11, PHP_CodeSniffer ^3.10 (PSR-12).

### Application Architecture
Layered, framework-light, with a clean separation between core domain/data and adapters:
- **Core (`src/`, `PhpDivingLog\` namespace)**:
  - `Model/` — immutable readonly DTOs (Dive, DiveSite, Personal, Certification, Tank, ...).
  - `Repository/` — PDO repositories that query the Diving Log schema **read-only**.
  - `Support/` — Config (env-based), Connection (PDO factory), UnitConverter, Formatter,
    MediaResolver, RtfConverter/HtmlSanitizer, ThumbnailGenerator, Translator, TextNormalizer.
- **Web adapter (`adapters/web/`, front controller `public/index.php`)**:
  - `Router` maps request URIs (and query-string mode) to route keys.
  - `Controller/*` build view-model arrays from repositories + support services.
  - `TwigRenderer` renders `templates/*.html.twig` and injects globals (e.g. `app_name`).
- **API adapter (`adapters/api/`, front controller `public/api.php`)**:
  - Controllers return JSON payloads (e.g. dive profile series consumed by the chart JS).
- **Bootstrap**: `adapters/web/bootstrap.php` wires config, the PDO connection, support
  services, and the repository set into a container array consumed by the front controllers.

### Data Storage
- **Primary storage**: MySQL database, populated by a dump exported from the Diving Log
  desktop application. All measurements are stored in **metric** units. Repositories are
  read-only and schema-agnostic where the export varies (optional columns resolved via
  `SELECT *` + case-insensitive aliases; alternate country→site mapping; inline vs.
  `DL_Tank` gas data; `Brevets` certifications).
- **Table prefix**: Configurable (`TABLE_PREFIX`, default `DL_`, validated at bootstrap);
  the prefix mechanism also enables multi-user hosting.
- **Caching / compilation**: Twig compiled cache under `var/cache/twig`. Image thumbnails
  are generated and cached on disk under `public/`.
- **Data formats**: HTML (Twig output), JSON (API), image files (JPG/GIF/PNG), and
  RTF-encoded comment fields.

### External Integrations
- **Diving Log desktop app** (divinglog.de): The upstream data source via MySQL export;
  phpDivingLog tracks its schema (referenced Diving Log version ~6.0.22).
- **Google Maps**: Dive-site coordinates are turned into a Google Maps search link (plain
  URL handling, no API integration).
- **Authentication**: None built in. Access control is expected at the web-server level.

### Monitoring & Dashboard Technologies
- **Rendering**: Server-side HTML via Twig; JSON via the API adapter. No SPA framework.
- **Client-side**: Vanilla JS — theme/palette controller, canvas charts (theme-aware,
  redraw on `themechange`), sortable/clickable tables, and a lightbox overlay.
- **Real-time**: None — each page reflects the current database state on request.
- **State management**: The MySQL database is the source of truth; app configuration is
  environment-based (`.env`); theme/palette preferences persist in `localStorage`.

## Development Environment

### Build & Development Tools
- **Build System**: No bundler is required to run; front-end assets are vendored. A small
  Node helper (`tools/build-assets.mjs`) can copy/refresh vendored assets when needed.
- **Package Management**: Composer (PHP). Front-end libraries are committed (vendored).
- **Development workflow**: Edit PHP/Twig/CSS/JS and refresh the browser. Twig cache under
  `var/cache/twig` is regenerated automatically.

### Code Quality Tools
- **Static Analysis**: PHPStan (`composer stan`).
- **Formatting/Standards**: PHP_CodeSniffer / PSR-12 (`composer cs`).
- **Testing Framework**: PHPUnit (`composer test`) — fixture-backed repository integration
  tests and end-to-end HTTP smoke tests (require `pdo_sqlite`).
- **One-shot gate**: `composer test && composer stan && composer cs`.

### Version Control & Collaboration
- **VCS**: Git, hosted on GitHub (Infern1/phpDivinglog).
- **Branching Strategy**: GitHub Flow — feature/work branches merged via pull requests
  (current active branch: `rewrite`).
- **Code Review Process**: Pull requests per `CONTRIBUTING.md`.

## Deployment & Distribution
- **Target Platform(s)**: Any LAMP/LEMP host — Apache or Nginx + MySQL + PHP >= 8.3. The web
  root points at `public/` (e.g. nginx `root .../public;`).
- **Distribution Method**: Clone the repository, run `composer install --no-dev`, and point
  the web root at `public/`.
- **Installation Requirements**:
  1. Web server (Apache/Nginx), MySQL, PHP >= 8.3, Composer.
  2. Import the Diving Log MySQL dump into a database.
  3. Copy `.env.example` to `.env` and set DB credentials, table prefix, units, and paths.
  4. Ensure `var/cache/` and image directories under `public/` are writable.
- **Update Mechanism**: Pull the latest code, re-run Composer if needed, and review the
  `CHANGELOG` for schema/config changes.

## Technical Requirements & Constraints

### Performance Requirements
- Suitable for personal-scale traffic (a diver's logbook), not high-concurrency workloads.
- Twig compile caching and on-disk thumbnail generation keep repeat page loads cheap.

### Compatibility Requirements
- **Platform Support**: Cross-platform PHP; runs on Apache and Nginx.
- **URL modes**: Supports both mod_rewrite pretty URLs and plain query-string URLs
  (`APP_QUERY_STRING`).
- **Dependency Versions**: PHP >= 8.3; PDO MySQL. Schema compatibility is tied to the
  Diving Log export format and handled defensively in the repositories.
- **Units**: Data is metric in storage; imperial display is an opt-in conversion layer.

### Security & Compliance
- **Security Requirements**: No built-in authentication — privacy relies on web-server-level
  controls. All DB access is via prepared PDO statements; repositories are read-only.
- **Compliance Standards**: None specifically targeted (personal-use hobby application).
- **Threat Model**: Primary concerns are safe request handling, safe media path resolution,
  and safe rendering of user-supplied RTF/comment content (sanitized before output).

### Scalability & Reliability
- **Expected Load**: Single-user or small multi-user logbooks; low traffic.
- **Availability**: Best-effort; depends on the host. No HA design.
- **Growth Projections**: Scales with logbook size; client-side table sorting and cached
  thumbnails keep large logbooks responsive.

## Technical Decisions & Rationale

### Decision Log
1. **Twig templating**: Replaced Smarty; separates HTML from PHP and gives a maintained,
   auto-escaping template engine.
2. **PDO repositories over `wpdb`**: Replaced the WordPress DB layer with typed, read-only
   PDO repositories and immutable DTOs.
3. **PSR-4 + Composer**: Adopted autoloading and dev tooling (PHPUnit/PHPStan/PHPCS) for a
   maintainable, testable codebase.
4. **`public/` web root**: Front controllers and web assets live under `public/`, matching
   modern hosting conventions and keeping core code outside the document root.
5. **Vendored front-end (Beer CSS, fonts) with no runtime CDN**: Preserves host-friendliness
   and offline rendering.
6. **Schema-agnostic repositories**: Defensive column/table resolution keeps the app working
   across real-world Diving Log export variations.
7. **Metric storage, display-time conversion**: Preserves fidelity to the export while
   allowing imperial presentation.

## Known Limitations
- **No built-in auth**: Privacy must be enforced outside the application.
- **Read-only scope**: The app displays data only; editing happens in the desktop program.
- **Export-schema coupling**: Behavior depends on the shape of the Diving Log export; new
  export variants may need additional defensive mapping.
- **Test coverage is growing**: HTTP smoke + repository tests exist; coverage should expand
  alongside new features.
